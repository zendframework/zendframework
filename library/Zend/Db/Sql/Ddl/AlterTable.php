<?php

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Exception;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

class AlterTable extends AbstractSql implements SqlInterface
{
    protected $table = '';
    protected $addColumns = array();
    protected $dropColumns = array();

    public function __construct($table = '')
    {
        ($table !== '') ?: $this->setTable($table);
    }

    public function setTable($name)
    {
        $this->table = $name;
    }

    public function addColumn(Column\ColumnInterface $column)
    {
        $this->addColumns[] = $column;
    }

    public function dropColumn($name)
    {
        $this->dropColumns[] = $name;
    }

    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // TODO: Implement getSqlString() method.
    }
}
