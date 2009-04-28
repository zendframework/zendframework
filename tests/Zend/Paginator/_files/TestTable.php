<?php
require_once 'Zend/Db/Table/Abstract.php';

class TestTable extends Zend_Db_Table_Abstract 
{
    protected $_primary = 'number';
    protected $_name = 'test';
}