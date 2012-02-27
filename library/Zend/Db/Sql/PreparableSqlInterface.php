<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface;

interface PreparableSqlInterface
{
    /**
     * @abstract
     * @param Adapter $adapter
     * @return StatementInterface
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement);
}
