<?php
/*
 *  The MIT License
 *
 *  Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

abstract class MwbExporter_Core_Model_Tables extends MwbExporter_Core_Model_Base
{
    protected $tables = array();
    
    public function __construct($data)
    {
        parent::__construct($data);
        
        // collect tables
        foreach($this->data->xpath("value") as $key => $node){
            $tmp = MwbExporter_Core_Registry::get('formatter')->createTable($node);
            
            if($tmp->isTranslationTable()){
                // skip translation tables
                continue;
            }
            
            $this->tables[] = $tmp;
        }
        
        /**
         * before you can check for foreign keys
         * you have to store all tables in the
         * object registry first
         */
        foreach($this->tables as $table){
            $table->checkForIndices();
            $table->checkForForeignKeys();
        }
        
        MwbExporter_Core_Registry::set($this->id, $this);
    }

}