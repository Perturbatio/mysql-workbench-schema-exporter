<?php
namespace MwbExporter\Formatter\Laravel\Helpers;

use MwbExporter\Writer\WriterInterface;
use MwbExporter\Helper\Comment;

/**
 * Created by kris with PhpStorm.
 * User: kris
 * Date: 22/01/15
 * Time: 21:09
 */
class PHPHelper {

	/**
	 * @param WriterInterface $writer
	 * @param                 $commentStr
	 * @param array           $commentVars
	 */
	public static function writeComment( WriterInterface $writer, $commentStr, $commentVars = array() ) {
		$writer->write( implode( "\n", Comment::wrap( strtr( $commentStr, $commentVars ), Comment::FORMAT_PHP ) ) );
	}

	/**
	 * @param WriterInterface $writer
	 * @param                 $name
	 * @param                 $writeCallback
	 */
	public static function writeFunction( WriterInterface $writer, $name, $comment, $writeCallback ) {
		static::writeComment( $writer, $comment, $commentVars = array() );
		$writer->write( 'function %s(){', $name )
			->indent()
			->write( '' )
			->writeCallback( $writeCallback )
			->write( '' )
			->outdent( '' )
			->write( '}' )
			->write( '' );
	}
}