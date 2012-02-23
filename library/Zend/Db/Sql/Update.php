<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

class Update implements SqlInterface, ParameterizedSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    protected $specification = 'UPDATE %1$s SET %2$s WHERE %3$s';

    protected $databaseOrSchema = null;
    protected $table = null;
    protected $emptyWhereProtection = true;
    protected $set = array();
    protected $where = null;

    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->table($table, $databaseOrSchema);
        }
    }

    public function table($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        if ($databaseOrSchema) {
            $this->databaseOrSchema = $databaseOrSchema;
        }
        return $this;
    }

    public function set(array $values, $flag = self::VALUES_SET)
    {
        if ($values == null) {
            throw new \InvalidArgumentException('set() expects an array of values');
        }

        if ($flag == self::VALUES_SET) {
            $this->set = array();
        }

        foreach ($values as $k => $v) {
            if (!is_string($k)) {
                throw new \Exception('set() expects a string for the value key');
            }
            $this->set[$k] = $v;
        }

        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function isValid($throwException = self::VALID_RETURN_BOOLEAN)
    {
        if ($this->table == null || !is_string($this->table)) {
            if ($throwException) throw new \Exception('A valid table name is required');
            return false;
        }

        if (count($this->values) == 0) {
            if ($throwException) throw new \Exception('Values are required for this insert object to be valid');
            return false;
        }

        if (count($this->columns) > 0 && count($this->columns) != count($this->values)) {
            if ($throwException) throw new \Exception('When columns are present, there needs to be an equal number of columns and values');
            return false;
        }

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

        $set = $this->set;
        if (is_array($set)) {
            $setSql = array();
            foreach ($set as $setName => $setValue) {
                $setSql[] = $platform->quoteIdentifier($setName) . ' = ' . $driver->formatParameterName($setName);
            }
            $set = implode(', ', $setSql);
        }

        $where = $this->where;
        if (is_array($where)) {
            $whereSql = array();
            foreach ($where as $whereName => $whereValue) {
                $whereSql[] = $platform->quoteIdentifier($whereName) . ' = ' . $driver->formatParameterName($whereName);
            }
            $where = implode(' AND ', $whereSql);
        }

        $sql = sprintf($this->specification, $table, $set, $where);
        return $adapter->getDriver()->getConnection()->prepare($sql);
    }

    public function getParameterContainer()
    {
        // @todo make sure this doen't clobber names
        return new ParameterContainer(array_merge($this->set, $this->where));
    }

    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $table = $platform->quoteIdentifier($this->table);

        if ($this->databaseOrSchema != '') {
            $table = $platform->quoteIdentifier($this->databaseOrSchema) . $platform->getIdentifierSeparator() . $table;
        }

        $set = $this->set;
        if (is_array($set)) {
            $setSql = array();
            foreach ($set as $setName => $setValue) {
                $setSql[] = $platform->quoteIdentifier($setName) . ' = ' . $platform->quoteValue($setName);
            }
            $set = implode(', ', $setSql);
        }

        $where = $this->where;
        if (is_array($where)) {
            $whereSql = array();
            foreach ($where as $whereName => $whereValue) {
                $whereSql[] = $platform->quoteIdentifier($whereName) . ' = ' . $platform->quoteValue($whereName);
            }
            $where = implode(' AND ', $whereSql);
        }

        return sprintf($this->specification, $table, $set, $where);
    }

}
