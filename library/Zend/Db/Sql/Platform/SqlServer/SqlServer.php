<?php

namespace Zend\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Platform\PlatformInterface,
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\Select;

class SqlServer implements PlatformInterface
{
    protected $adapter = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function supportsSqlObject(PreparableSqlInterface $sqlObject)
    {
        if ($sqlObject instanceof Select) {
            return true;
        }
        return false;
    }

    public function prepareStatementFromSqlObject(PreparableSqlInterface $sqlObject, StatementInterface $statement = null)
    {
        if ($sqlObject instanceof Select) {
            $selectProxy = new SelectProxy($sqlObject);
            $selectProxy->prepareStatement($this->adapter, ($statement) ?: $this->adapter->createStatement());
        }
        return $statement;
    }
}
