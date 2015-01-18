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

namespace MwbExporter\Formatter\Laravel;

use MwbExporter\Formatter\DatatypeConverter as BaseDatatypeConverter;

class DatatypeConverter extends BaseDatatypeConverter
{
    public function setup()
    {
        $this->register(array(
            static::DATATYPE_TINYINT            => 'tinyInteger',
            static::DATATYPE_SMALLINT           => 'smallInteger',
            static::DATATYPE_MEDIUMINT          => 'mediumInteger',
            static::DATATYPE_INT                => 'integer',
            static::DATATYPE_BIGINT             => 'bigInteger',
            static::DATATYPE_FLOAT              => 'float',
            static::DATATYPE_DOUBLE             => 'double',
            static::DATATYPE_DECIMAL            => 'decimal',
            static::DATATYPE_CHAR               => 'char',
            static::DATATYPE_VARCHAR            => 'string',
            static::DATATYPE_BINARY             => 'binary',
            static::DATATYPE_VARBINARY          => 'string',//laravel lacks varbinary, see: https://github.com/laravel/framework/issues/3297
            static::DATATYPE_TINYTEXT           => 'string',
            static::DATATYPE_TEXT               => 'text',
            static::DATATYPE_MEDIUMTEXT         => 'mediumText',//'clob(16777215)',
            static::DATATYPE_LONGTEXT           => 'longText',
            static::DATATYPE_TINYBLOB           => 'binary',//'blob(255)',
            static::DATATYPE_BLOB               => 'binary',//'blob(65535)',
            static::DATATYPE_MEDIUMBLOB         => 'binary',//'blob(16777215)',
            static::DATATYPE_LONGBLOB           => 'binary',//'blob',
            static::DATATYPE_DATETIME           => 'dateTime',
            static::DATATYPE_DATETIME_F         => 'timestamp',
            static::DATATYPE_DATE               => 'date',
            static::DATATYPE_TIME               => 'time',
            static::DATATYPE_YEAR               => 'integer',
            static::DATATYPE_TIMESTAMP          => 'timestamp',
            static::DATATYPE_GEOMETRY           => 'geometry',
            static::DATATYPE_LINESTRING         => 'linestring',
            static::DATATYPE_POLYGON            => 'polygon',
            static::DATATYPE_MULTIPOINT         => 'multipoint',
            static::DATATYPE_MULTILINESTRING    => 'multilinestring',
            static::DATATYPE_MULTIPOLYGON       => 'multipolygon',
            static::DATATYPE_GEOMETRYCOLLECTION => 'geometrycollection',
            static::DATATYPE_BIT                => 'bit',
            static::DATATYPE_ENUM               => 'enum',
            static::DATATYPE_SET                => 'set',
            static::USERDATATYPE_BOOLEAN        => 'boolean',
            static::USERDATATYPE_BOOL           => 'boolean',
            static::USERDATATYPE_FIXED          => 'decimal',
            static::USERDATATYPE_FLOAT4         => 'float',
            static::USERDATATYPE_FLOAT8         => 'double',
            static::USERDATATYPE_INT1           => 'tinyInteger',
            static::USERDATATYPE_INT2           => 'smallInteger',
            static::USERDATATYPE_INT3           => 'mediumInteger',
            static::USERDATATYPE_INT4           => 'integer',
            static::USERDATATYPE_INT8           => 'bigInteger',
            static::USERDATATYPE_INTEGER        => 'integer',
            static::USERDATATYPE_LONGVARBINARY  => 'binary',
            static::USERDATATYPE_LONGVARCHAR    => 'longText',
            static::USERDATATYPE_LONG           => 'bigInteger',
            static::USERDATATYPE_MIDDLEINT      => 'mediumInteger',
            static::USERDATATYPE_NUMERIC        => 'decimal',
            static::USERDATATYPE_DEC            => 'decimal',
            static::USERDATATYPE_CHARACTER      => 'char',
        ));
    }
}