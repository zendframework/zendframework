<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

class Insert implements SqlInterface, ParameterizedSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    protected $specification = 'INSERT INTO %1$s (%2$s) VALUES (%3$s)';

    protected $databaseOrSchema = null;

    protected $table = null;

    protected $columns = array();

    protected $values = array();

    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->into($table, $databaseOrSchema);
        }
    }

    public function into($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        if ($databaseOrSchema) {
            $this->databaseOrSchema = $databaseOrSchema;
        }
        return $this;
    }

    public function columns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function values(array $values, $flag = self::VALUES_SET)
    {
        if ($values == null) {
            throw new \InvalidArgumentException('values() expects an array of values');
        }

        $keys = array_keys($values);
        $firstKey = current($keys);

        if (is_string($firstKey)) {
            $this->columns($keys);
            $values = array_values($values);
        } elseif (is_int($firstKey)) {
            $values = array_values($values);
        }

        if ($flag == self::VALUES_MERGE) {
            $this->values = array_merge($this->values, $values);
        } else {
            $this->values = $values;
        }

        return $this;
    }

    public function __set($name, $value)
    {
        $values = array($name => $value);
        $this->values($values, self::VALUES_MERGE);
        return $this;
    }

    public function __unset($name)
    {
        if (!($position = array_search($name, $this->columns))) {
            throw new \InvalidArgumentException('Not in statement');
        }

        unset($this->columns[$position]);
        unset($this->values[$name]);
    }

    public function __isset($name)
    {
        return in_array($name, $this->columns);
    }

    public function __get($name)
    {
        if (!($position = array_search($name, $this->columns))) {
            throw new \InvalidArgumentException('Not in statement');
        }
        return $this->values[$name];
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

    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $table = $platform->quoteIdentifier($this->table);

        if ($this->databaseOrSchema != '') {
            $table = $platform->quoteIdentifier($this->databaseOrSchema) . $platform->getIdentifierSeparator() . $table;
        }

        $columns = array_map(array($platform, 'quoteIdentifier'), $this->columns);
        $columns = implode(', ', $columns);

        $values = array_map(array($platform, 'quoteValue'), $this->values);
        $values = implode(', ', $values);

        return sprintf($this->specification, $table, $columns, $values);
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

        $columns = array_map(array($platform, 'quoteIdentifier'), $this->columns);
        $columns = implode(', ', $columns);

        $values = array_map(array($driver, 'formatParameterName'), $this->columns);
        $values = implode(', ', $values);

        $sql = sprintf($this->specification, $table, $columns, $values);
        return $adapter->getDriver()->getConnection()->prepare($sql);
    }

    public function getParameterContainer()
    {
        return new ParameterContainer(array_combine($this->columns, $this->values));
    }

}
