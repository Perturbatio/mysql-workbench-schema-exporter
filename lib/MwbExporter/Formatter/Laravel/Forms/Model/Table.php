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

namespace MwbExporter\Formatter\Laravel\Forms\Model;

use MwbExporter\Model\Table as BaseTable;
use MwbExporter\Writer\WriterInterface;
use MwbExporter\Formatter\Laravel\Migrations\Formatter;
use MwbExporter\Helper\Comment;

class Table extends BaseTable
{
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
	 * Get table file name.
	 *
	 * @param string $format  The filename format
	 * @param array $vars  The overriden variables
	 * @return string
	 */
	public function getTableFileName($format = null, $vars = array())
	{
		/*
		if (0 === strlen($filename = $this->getDocument()->translateFilename($format, $this, $vars)))
		{
			$filename = 'models/'.implode('.', array($this->getSchema()->getName(), $this->getRawTableName(), $this->getFormatter()->getFileExtension()));
		}
		*/
		$filename = 'forms/'.implode('.', array( $this->getSchema()->getName(), $this->getRawTableName(), $this->getFormatter()->getFileExtension()));
		return $filename;
	}

	public function writeTable(WriterInterface $writer)
	{
		if (!$this->isExternal()) {
			$table_name = ($this->getConfig()->get(Formatter::CFG_EXTEND_TABLENAME_WITH_SCHEMA) ? $this->getSchema()->getName().'.' : '').$this->getRawTableName();
			$class_name  = $this->getModelName(); //= str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
			$lower_class = strtolower( $class_name );

			$writer
				->open($this->getTableFileName())
				->write('<?php namespace App\Forms;')
				->write('')
				->write('use Kris\LaravelFormBuilder\Form;')
				->write('')
/*
 * <?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Add fields here...
    }
}
 * */
				->writeCallback(function(WriterInterface $writer, Table $_this = null) {
					if ($_this->getConfig()->get(Formatter::CFG_ADD_COMMENT)) {
						$writer
							->write($_this->getFormatter()->getComment(Comment::FORMAT_PHP))
							->write('')
						;
					}
				})

				->write('class %sForm extends Form {', $class_name)
				->write('')
					->indent()
					->write('public function buildForm(){')
						->indent()
							->write('')
							->write('')
							->writeCallback(function(WriterInterface $writer, Table $_this = null) {
								$_this->getColumns()->write($writer);
							})
						->write('')
						->outdent()
					->write('}')
					->write('')
				->outdent()
				->write('}')
				->write('')

				->close()
			;

			return self::WRITE_OK;
		}

		return self::WRITE_EXTERNAL;
	}
}