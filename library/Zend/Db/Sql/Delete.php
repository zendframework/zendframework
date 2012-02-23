<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

class Delete implements SqlInterface, ParameterizedSqlInterface
{
    protected $specification = 'DELETE FROM %1$s WHERE %2$s';

    protected $databaseOrSchema = null;
    protected $table = null;
    protected $emptyWhereProtection = true;
    protected $set = array();
    protected $where = null;

    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->from($table, $databaseOrSchema);
        }
    }

    public function from($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        if ($databaseOrSchema) {
            $this->databaseOrSchema = $databaseOrSchema;
        }
        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function isValid()
    {
        // @todo
        return true;
    }

    public function getParameterizedSqlString(Adapter $adapter)
    {
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $table = $platform->quoteIdentifier($this->table);
        if ($this->databaseOrSchema != '') {
            $table = $platform->quoteIdentifier($this->databaseOrSchema)
                . $platform->getIdentifierSeparator()
                . $table;
        }

        $where = $this->where;
        if (is_array($where)) {
            $whereSql = array();
            foreach ($where as $whereName => $whereValue) {
                $whereSql[] = $platform->quoteIdentifier($whereName) . ' = ' . $driver->formatParameterName($whereName);
            }
            $where = implode(' AND ', $whereSql);
        }

        $sql = sprintf($this->specification, $table, $where);
        return $adapter->getDriver()->getConnection()->prepare($sql);
    }

    public function getParameterContainer()
    {
        return new ParameterContainer(array_merge($this->set, $this->where));
    }

    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $table = $platform->quoteIdentifier($this->table);

        if ($this->databaseOrSchema != '') {
            $table = $platform->quoteIdentifier($this->databaseOrSchema) . $platform->getIdentifierSeparator() . $table;
        }

        $where = $this->where;
        if (is_array($where)) {
            $whereSql = array();
            foreach ($where as $whereName => $whereValue) {
                $whereSql[] = $platform->quoteIdentifier($whereName) . ' = ' . $platform->quoteValue($whereName);
            }
            $where = implode(' AND ', $whereSql);
        }

        return sprintf($this->specification, $table, $where);
    }

}
