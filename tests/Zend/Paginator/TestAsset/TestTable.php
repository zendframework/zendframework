<?php

namespace ZendTest\Paginator\TestAsset;

use Zend\Db\TableGateway;

class TestTable extends TableGateway\TableGateway
{
    protected $_primary = 'number';
    protected $_name = 'test';
}
