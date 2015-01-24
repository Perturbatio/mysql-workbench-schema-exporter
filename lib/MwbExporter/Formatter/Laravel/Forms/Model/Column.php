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

use MwbExporter\Model\Column as BaseColumn;
use MwbExporter\Writer\WriterInterface;

class Column extends BaseColumn {


	/**
	 * @param WriterInterface $writer
	 *
	 * @return $this
	 */
	public function write( WriterInterface $writer ) {

		$column_name = $this->getColumnName();
		$column_type = $this->getFormatter()
			->getDatatypeConverter()
			->getType( $this );

		$handlerMethod = 'handle_' . $column_type;
		/*$this
			->add('name', 'text')
			->add('lyrics', 'textarea')
			->add('publish', 'checkbox');
		*/
		$writer->writeIf( $comment = $this->getParameters()
			->get( 'comment' ), '// %s', $comment );
		$this->{$handlerMethod}( $column_name, $writer );
		$writer->write( '' );

		return $this;

	}

	/**
	 * @return mixed
	 */
	protected function getDefault() {

		return $this->getParameters()
			->get( 'defaultValue', NULL );

	}

	public function get_options_string( $options ) {

		$options[ 'class' ] = implode( ' ', $options[ 'class' ] );

		$options_string = '[ ';

		$options_string .= implode( ', ', array_map( function ( $v, $k ) {
			if ( !is_array( $v ) ){
				return "'$k' => '$v'";
			}
			else {
				$array = '[ ';
				$array .= implode( ', ', array_map( function ( $value ) {
					return "$value" . '=>' . "$value";
				}, $v ) );
				$array .= ' ]';

				return "'$k' => $array";
			}
		}, $options, array_keys( $options ) ) );

		$options_string .= ' ]';

		return $options_string;
	}


	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 * @param string          $typeFn
	 */
	public function handle_integer( $column_name, WriterInterface $writer, $typeFn = 'integer' ) {

		$output             = '$this->add(\'%s\', \'text\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( $this->isPrimary() || $this->isAutoIncrement() ){
			$options[ 'type' ] = 'hidden';
		}

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}
		if ( $this->isUnsigned() ){
			$options[ 'class' ][ ] = 'unsigned';
		}

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = $default;
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );

	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_tinyInteger( $column_name, WriterInterface $writer ) {
		$this->handle_integer( $column_name, $writer, 'tiny' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_smallInteger( $column_name, WriterInterface $writer ) {
		$this->handle_integer( $column_name, $writer, 'small' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_mediumInteger( $column_name, WriterInterface $writer ) {
		$this->handle_integer( $column_name, $writer, 'medium' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_bigInteger( $column_name, WriterInterface $writer ) {
		$this->handle_integer( $column_name, $writer, 'bigInteger' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_boolean( $column_name, WriterInterface $writer ) {

		$output             = '$this->add(\'%s\', \'checkbox\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_boolean' ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = $default;
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );

	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 * @param string          $typeFn
	 * @param int             $total
	 * @param int             $places
	 */
	public function handle_float( $column_name, WriterInterface $writer, $typeFn = 'float', $total = 8, $places = 2 ) {

		$output             = '$this->add(\'%s\', \'text\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}


		$options[ 'data-total' ]  = $total;
		$options[ 'data-places' ] = $places;

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = $default;
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_double( $column_name, WriterInterface $writer ) {
		$this->handle_float( $column_name, $writer, 'double', NULL, NULL );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_decimal( $column_name, WriterInterface $writer ) {
		$this->handle_float( $column_name, $writer, 'decimal' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 * @param string          $typeFn
	 * @param int             $maxLength
	 */
	public function handle_char( $column_name, WriterInterface $writer, $typeFn = 'char', $maxLength = 4 ) {

		$output             = '$this->add(\'%s\', \'text\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$options[ 'maxlength' ] = $maxLength;

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = addslashes( trim( $default, '\'"' ) );
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_string( $column_name, WriterInterface $writer ) {
		$this->handle_char( $column_name, $writer, 'string', 100 );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_binary( $column_name, WriterInterface $writer, $typeFn = 'binary' ) {
		//input type file?
		$writer->write('// input type file');
		return;
		$output             = '$this->add(\'%s\', \'text\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = addslashes( trim( $default, '\'"' ) );
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );

	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 * @param string          $typeFn
	 */
	public function handle_text( $column_name, WriterInterface $writer, $typeFn = 'text' ) {

		$output             = '$this->add(\'%s\', \'textarea\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = addslashes( trim( $default, '\'"' ) );
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_mediumText( $column_name, WriterInterface $writer ) {
		$this->handle_text( $column_name, $writer, 'mediumText' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_longText( $column_name, WriterInterface $writer ) {
		$this->handle_text( $column_name, $writer, 'longText' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 * @param string          $typeFn
	 */
	public function handle_dateTime( $column_name, WriterInterface $writer, $typeFn = 'dateTime' ) {


		$output             = '$this->add(\'%s\', \'text\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = addslashes( trim( $default, '\'"' ) );
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );

	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_timestamp( $column_name, WriterInterface $writer ) {
		$this->handle_dateTime( $column_name, $writer, 'timestamp' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_date( $column_name, WriterInterface $writer ) {
		$this->handle_dateTime( $column_name, $writer, 'date' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_time( $column_name, WriterInterface $writer ) {
		$this->handle_dateTime( $column_name, $writer, 'time' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_enum( $column_name, WriterInterface $writer, $typeFn = 'enum' ) {


		$output             = '$this->add(\'%s\', \'choice\', %s);';
		$options            = [ ];
		$options[ 'class' ] = [ 'field_type_' . $typeFn ];


		$enum_options = trim( $this->getParameters()
			->get( 'datatypeExplicitParams' ), '()' );

		$enum_values = explode( ',', $enum_options );

		$options[ 'choices' ] = [ ];

		foreach ( $enum_values as $value ) {
			$options[ 'choices' ][ $value ] = $value;
		}

		if ( !$this->isNotNull() ){
			$options[ 'class' ][ ] = 'required';
			$options[ 'required' ] = 'required';
		}

		$options[ 'multiple' ] = ( $typeFn === 'set' ) ? 'true' : 'false';

		$default = $this->getDefault();

		if ( $default == 0 || !empty( $default ) ){
			$options[ 'value' ] = addslashes( trim( $default, '\'"' ) );
		}

		$options_string = $this->get_options_string( $options );

		$writer->write( $output, $column_name, $options_string );

	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_set( $column_name, WriterInterface $writer ) {

		$writer->write( '//the set datatype is not yet handled (column: %s) by laravel', $column_name );
		$this->handle_enum( $column_name, $writer, 'set' );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_geometry( $column_name, WriterInterface $writer ) {
		$writer->write( '//the geometry datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_linestring( $column_name, WriterInterface $writer ) {
		$writer->write( '//the linestring datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_polygon( $column_name, WriterInterface $writer ) {
		$writer->write( '//the polygon datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_multipoint( $column_name, WriterInterface $writer ) {
		$writer->write( '//the multipoint datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_multilinestring( $column_name, WriterInterface $writer ) {
		$writer->write( '//the multilinestring datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_multipolygon( $column_name, WriterInterface $writer ) {
		$writer->write( '//the multipolygon datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_geometrycollection( $column_name, WriterInterface $writer ) {
		$writer->write( '//the geometrycollection datatype is not yet handled (column: %s)', $column_name );
	}

	/**
	 * @param                 $column_name
	 * @param WriterInterface $writer
	 */
	public function handle_bit( $column_name, WriterInterface $writer ) {
		$writer->write( '//the bit datatype is not yet handled (column: %s)', $column_name );
	}
}