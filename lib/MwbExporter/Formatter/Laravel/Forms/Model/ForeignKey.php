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

use MwbExporter\Model\ForeignKey as BaseForeignKey;
use Doctrine\Common\Inflector\Inflector;
use MwbExporter\Writer\WriterInterface;

class ForeignKey extends BaseForeignKey
{
    public function getForeignAlias()
    {
        return trim($this->parseComment('foreignAlias'));
    }

    public function write(WriterInterface $writer)
    {

        if ($this->referencedTable == null) {
            $writer->write('// There is another foreign key declaration.');
        } else {
            $local = array_shift($this->getLocals());
            $foreign = array_shift($this->getForeigns());

            $onDelete = strtolower($this->parameters->get('deleteRule'));
            $onUpdate = strtolower($this->parameters->get('updateRule'));

            $writer->write(
                '$table->foreign(\'%s\')->references(\'%s\')->on(\'%s\')',
                $local->getColumnName(),
                $foreign->getColumnName(),
                $foreign->getTable()->getName()
            );

            $writer->write('->onDelete(\'%s\')', $onDelete);
            $writer->write('->onUpdate(\'%s\')', $onUpdate);
            $writer->write(';');
        }

        return $this;
    }
}