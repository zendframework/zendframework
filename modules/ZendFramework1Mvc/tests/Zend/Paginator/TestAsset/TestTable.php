<?php

namespace ZendTest\Paginator\TestAsset;

class TestTable extends \Zend\Db\Table\AbstractTable
{
    protected $_primary = 'number';
    protected $_name = 'test';
}