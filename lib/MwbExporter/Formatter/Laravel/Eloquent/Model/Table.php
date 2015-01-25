<?php

/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012-2014 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace MwbExporter\Formatter\Laravel\Eloquent\Model;

use MwbExporter\Model\Table as BaseTable;
use MwbExporter\Writer\Writer;
use MwbExporter\Writer\WriterInterface;
use MwbExporter\Formatter\Laravel\Migrations\Formatter;
use MwbExporter\Helper\Comment;
use Doctrine\Common\Inflector\Inflector;
use MwbExporter\Formatter\Laravel\Helpers\PHPHelper;


class Table extends BaseTable
{
	public $primaryIsId = true;
	public $modelExtends = 'Eloquent';

	protected $referencedColumns = [];

	public function getActAsBehaviour()
	{
		return trim($this->parseComment('actAs'));
	}

	public function getExternalRelations()
	{
		// processing external Relation
		// {d:externalRelations}[..]{/d:externalRelations}
		return trim($this->parseComment('externalRelations'));
	}

	public function getModelName()
	{
		if ($this->isExternal()) {
			return $this->getRawTableName();
		} else {
			return parent::getModelName();
		}
	}

	/**
	 * @return array
	 */
	public function getMorphTo(){

		return explode('|', trim($this->parseComment('morphTo')));

	}


	/**
	 * @return array
	 */
	public function getMorphMany(){
		return explode('|', trim($this->parseComment('morphMany')));
	}

	/**
	 * @return array
	 */
	public function getMorphOne(){

		return explode('|', trim($this->parseComment('morphOne')));

	}


	/**
	 * @return array
	 */
	public function getHasMany(){
		return explode('|', trim($this->parseComment('hasMany')));
	}

	/**
	 * Get table file name.
	 *
	 * @param string $format  The filename format
	 * @param array $vars  The overriden variables
	 * @return string
	 */
	public function getTableFileName($format = null, $vars = array())
	{

		if (0 === strlen($filename = $this->getDocument()->translateFilename($format, $this, $vars)))
		{
			$filename = implode('.', array($this->getSchema()->getName(), $this->getRawTableName(), $this->getFormatter()->getFileExtension()));
		}


		return 'models/'.$filename;
	}

	protected function findReferencedColumns(){

		$columns = $this->getColumns();
		/**
		 * @var $column \MwbExporter\Model\Columns
		 */
		foreach($columns as $column){
			//$column->hasOneToManyRelation();
		}
		/*
		if ($this->hasOneToManyRelation()){
			echo $this->getName(), '::';
			print_r($this->foreigns);
		}
		*/
	}


	/**
	 * @param WriterInterface $writer
	 *
	 * @return int
	 */
	public function writeTable(WriterInterface $writer)
	{
		$this->initManyToManyRelations();

		if (!$this->isExternal() && !$this->isManyToMany()) {
			$table_name = ($this->getConfig()->get(Formatter::CFG_EXTEND_TABLENAME_WITH_SCHEMA) ? $this->getSchema()->getName().'.' : '').$this->getRawTableName();
			$class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));

			$writer
				->open($this->getTableFileName())
				->write('<?php')
				->write('// namespace here')
				->write('')
				->write('use \%s;', $this->modelExtends)
				->write('')

				->writeCallback(function(WriterInterface $writer, Table $_this = null) {
					if ($_this->getConfig()->get(Formatter::CFG_ADD_COMMENT)) {
						$writer
							->write($_this->getFormatter()->getComment(Comment::FORMAT_PHP))
							->write('')
						;
					}
				})

				->write('class %s extends %s {', Inflector::singularize($class_name), $this->modelExtends)
				->write('')
				->indent()
				->write('')
				->write('// add any traits here')
				->write('')
				->writeCallback(function(WriterInterface $writer, Table $_this = null) {
					$_this->getColumns()->write($writer);
				});
			$this->writeRelationships($writer);


			$writer->outdent()
				->write('}')
				->write('')

				->close()
			;

			return self::WRITE_OK;
		}

		//var_dump($this->getSchema());

		return self::WRITE_EXTERNAL;
	}

	/**
	 * @param WriterInterface $writer
	 * @param                 $commentStr
	 * @param array           $commentVars
	 */
	public function writeComment(WriterInterface $writer, $commentStr, $commentVars = array()){
		$writer->write(implode("\n", Comment::wrap(strtr($commentStr, $commentVars), Comment::FORMAT_PHP)));
	}

	/**
	 * @param WriterInterface $writer
	 * @param                 $name
	 * @param                 $writeCallback
	 */
	protected function writeFunction(WriterInterface $writer, $name, $comment, $writeCallback){
		$this->writeComment($writer, $comment, $commentVars = array() );
		$writer->write('function %s(){', $name)
			->indent()
			->write('')
			->writeCallback($writeCallback)
			->write('')
			->outdent('')
			->write('}')
			->write('');
	}

	/**
	 * @param WriterInterface $writer
	 */
	protected function writeHasMany( WriterInterface $writer ) {
		$hasManys = $this->getHasMany();
		foreach($hasManys as $hasMany) {
			if ( !empty( $hasMany ) ){

				//list( $model, $relation ) = explode( ':', $hasMany );

				//if ( !empty( $hasMany )  ){
				$this->writeFunction( $writer, ( Inflector::pluralize( ucfirst( $hasMany ) ) ), '@return \Illuminate\Database\Eloquent\Relations\HasMany', function ( WriterInterface $writer ) use ( $hasMany ) {
					$writer->write( 'return $this->hasMany(\'%s\');', Inflector::singularize( ucfirst( $hasMany ) ));
				} );
				//}
			}
		}
	}

	/**
	 * @param WriterInterface $writer
	 */
	protected function writeMorphTo(WriterInterface $writer){

		$morphTos = $this->getMorphTo();

		foreach($morphTos as $morphTo) {
			if ( !empty( $morphTo ) ){

				PHPHelper::writeFunction( $writer, ucfirst($morphTo), '@return \Illuminate\Database\Eloquent\Relations\MorphTo', function ( WriterInterface $writer ) {
					$writer->write( 'return $this->morphTo();' );
				} );
			}
		}

	}

	/**
	 * @param WriterInterface $writer
	 */
	protected function writeMorphMany( WriterInterface $writer ) {
		$morphManys = $this->getMorphMany();
		foreach($morphManys as $morphMany) {
			if ( !empty( $morphMany ) ){

				list( $model, $relation ) = explode( ':', $morphMany );

				if ( !empty( $model ) && !empty( $relation ) ){
					$this->writeFunction( $writer, ( Inflector::pluralize( ucfirst($model) ) ), '@return \Illuminate\Database\Eloquent\Relations\MorphMany', function ( WriterInterface $writer ) use ( $model, $relation ) {
						$writer->write( 'return $this->morphMany(\'%s\',\'%s\');', Inflector::singularize( ucfirst( $model ) ), $relation );
					} );
				}
			}
		}
	}

	/**
	 * @param WriterInterface $writer
	 */
	protected function writeMorphOne( WriterInterface $writer ) {
		$morphOnes = $this->getMorphOne();
		foreach($morphOnes as $morphOne) {
			if ( !empty( $morphOne ) ){

				list( $model, $relation ) = explode( ':', $morphOne );

				if ( !empty( $model ) && !empty( $relation ) ){
					$this->writeFunction( $writer, ( Inflector::pluralize( $model ) ), '@return \Illuminate\Database\Eloquent\Relations\MorphOne', function ( WriterInterface $writer ) use ( $model, $relation ) {
						$writer->write( 'return $this->morphOne(\'%s\',\'%s\');', Inflector::singularize( ucfirst( $model ) ), $relation );
					} );
				}
			}
		}
	}

	/**
	 * @param WriterInterface $writer
	 */
	protected function writeRelationships(WriterInterface $writer){

		//$externalRelation = $this->getExternalRelations();
		$table_relations = $this->getTableRelations();

		if (count($table_relations) ){//|| $externalRelation) {


			foreach ($table_relations as $relation) {
				$writer->write('//table_relation');
				$relation->write($writer);
			}
			/*
						if ($externalRelation) {
							$writer->write('//external_relation');
							$writer->write($externalRelation);
						}
			*/
		}

		$this->writeHasMany($writer);

		$this->writeMorphTo($writer);
		$this->writeMorphMany($writer);
		$this->writeMorphOne($writer);


		/*
		$writer->writeIf($actAs = trim($this->getActAsBehaviour()), $actAs)

			->writeCallback(function(WriterInterface $writer, Table $_this = null) {
				$_this->getColumns()->write($writer);
			})

			->writeCallback(function(WriterInterface $writer, Table $_this = null) {

				$externalRelation = $_this->getExternalRelations();

				if (count($_this->getTableRelations()) || $externalRelation) {
					$writer->write('// foreign relations');

					foreach ($_this->getTableRelations() as $relation) {
						$relation->write($writer);
					}

					if ($externalRelation) {
						$writer->write($externalRelation);
					}

				}
			})

			->write('')

			->writeCallback(function(WriterInterface $writer, Table $_this = null) {
				if (count($_this->getTableIndices())) {
					$writer->write('// indices');
					foreach ($_this->getTableIndices() as $index) {
						$index->write($writer);
					}
					$writer->write('');

				}
			})


			->write('');
		*/
	}


}