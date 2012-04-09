<?php

namespace Zend\Db\Sql\Platform\Mysql;

use Zend\Db\Sql\Platform\PlatformInterface;

class Mysql implements PlatformInterface
{

    public function create($sqlObject)
    {
        if ($sqlObject instanceof \Zend\Db\Sql\Select) {
            return new Select($sqlObject);
        }
    }

}