<?php

namespace Zend\Db\Sql\Platform\Mysql;

use Zend\Db\Sql\Select;

class SelectPreparer extends Select
{

    protected $select = null;

    public function setProxy(Select $select)
    {
        $this->select = $select;
    }

}