<?php

namespace Zend\Db\Sql\Platform\Mysql;

use Zend\Db\Sql\Select as BaseSelect;

class Select extends BaseSelect
{

    protected $baseSelect = null;

    public function __construct(BaseSelect $select)
    {
        $this->baseSelect = $select;
    }

}