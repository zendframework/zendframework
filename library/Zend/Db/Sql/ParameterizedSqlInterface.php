<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter;

interface ParameterizedSqlInterface
{
    public function getParameterizedSqlString(Adapter $adapter);
    public function getParameterContainer();
}
