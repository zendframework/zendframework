<?php

namespace Zend\Db\Sql\Platform\Mysql;

use Zend\Db\Sql\Platform\PlatformInterface;

class Mysql implements PlatformInterface
{

    public function __construct()
    {

    }

    function canPrepareSqlObject(PreparableSqlInterface $sqlObject)
    {
        // TODO: Implement canPrepareSqlObject() method.
    }

    public function prepareSqlObject(PreparableSqlInterface $sqlObject)
    {
        // TODO: Implement prepareSqlObject() method.
    }
}