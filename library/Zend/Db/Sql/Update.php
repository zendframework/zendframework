<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

class Update implements SqlInterface, PreparableSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    protected $specification = 'UPDATE %1$s SET %2$s';

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
        $this->where = new Where();
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

    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND)
    {
        if ($predicate instanceof Where) {
            $this->where = $predicate;
        } elseif ($predicate instanceof \Closure) {
            $predicate($this->where);
        } else {
            if (is_string($predicate)) {
                $predicate = new Predicate\Literal($predicate);
            } elseif (is_array($predicate)) {
                foreach ($predicate as $pkey => $pvalue) {
                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        $predicate = new Predicate\Literal($pkey, $pvalue);
                    } elseif (is_string($pkey)) {
                        $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                    } else {
                        $predicate = new Predicate\Literal($pvalue);
                    }
                }
            }
            $this->where->addPredicate($predicate, $combination);
        }
        return $this;
    }

    /*
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
    */

    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statement->getParameterContainer();
        $prepareType = $driver->getPrepareType();

        $table = $platform->quoteIdentifier($this->table);
        if ($this->databaseOrSchema != '') {
            $table = $platform->quoteIdentifier($this->databaseOrSchema)
                . $platform->getIdentifierSeparator()
                . $table;
        }

        $set = $this->set;
        if (is_array($set)) {
            $setSql = array();
            $values = array();
            foreach ($set as $column => $value) {
                if ($prepareType == 'positional') {
                    $parameterContainer->offsetSet(null, $value);
                    $name = $driver->formatParameterName(null);
                } elseif ($prepareType == 'named') {
                    $parameterContainer->offsetSet($column, $value);
                    $name = $driver->formatParameterName($column);
                }
                $setSql[] = $platform->quoteIdentifier($column) . ' = ' . $name;
            }
            $set = implode(', ', $setSql);
        }

        $sql = sprintf($this->specification, $table, $set);
        $statement->setSql($sql);

        $this->where->prepareStatement($adapter, $statement);
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

        return sprintf($this->specification, $table, $set, $this->where->getSqlString($platform));
    }

}
