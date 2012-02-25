<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;


class Select implements SqlInterface, ParameterizedSqlInterface
{
    protected $specification = '%1$s %2$s %3$s %4$s';
    protected $specification1 = 'SELECT %1$s FROM %2$s';
    protected $specification2 = 'WHERE %1$s';
    protected $specification3 = 'ORDER BY %1$s';
    protected $specification4 = 'FETCH %1$s';

    protected $table = null;
    protected $databaseOrSchema = null;
    protected $columns = null;
    protected $where = null;
    protected $order = null;

    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->from($table, $databaseOrSchema);
        }
    }

    public function from($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        $this->databaseOrSchema = $databaseOrSchema;
    }

    public function columns($cols = '*', $correlationName = null)
    {
        // @todo
    }

    public function union($select = array(), $type = self::SQL_UNION)
    {
        // @todo
    }

    public function join($name, $cond, $cols = self::SQL_WILDCARD, $schema = null, $type = null)
    {
        // @todo
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function getParameterizedSqlString(Adapter $adapter)
    {
        // replace with Db\Sql select
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $columns = '*';

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

        $order = null;
        $limit = null;

        $s1 = sprintf($this->specification1, $columns, $table);

        $s2 = (isset($where)) ? sprintf($this->specification2, $where) : '';
        $s3 = (isset($order)) ? sprintf($this->specification3, $order) : '';
        $s4 = (isset($limit)) ? sprintf($this->specification3, $limit) : '';

        $sql = trim(sprintf($this->specification, $s1, $s2, $s3, $s4));
        return $adapter->getDriver()->getConnection()->prepare($sql);
    }

    public function getParameterContainer()
    {
        $where = (is_array($this->where)) ? $this->where : array();
        return new ParameterContainer($where);
    }

    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $table = $platform->quoteIdentifier($this->table);

        $columns = '*';

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

        $order = null;
        $limit = null;

        $s1 = sprintf($this->specification1, $columns, $table);
        $s2 = sprintf($this->specification2, $where);

        $s3 = (isset($order)) ? sprintf($this->specification3, $order) : '';
        $s4 = (isset($limit)) ? sprintf($this->specification3, $limit) : '';

        $sql = sprintf($this->specification, $s1, $s2, $s3, $s4);
        return $sql;
    }
}