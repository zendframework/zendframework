<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Platform\Sql92;
use Zend\Db\Adapter\StatementContainerInterface;

class Insert extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    /**#@+
     * Constants
     *
     * @const
     */
    const SPECIFICATION_INSERT = 'insert';
    const SPECIFICATION_SELECT = 'select';
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';
    /**#@-*/

    /**
     * @var array Specification array
     */
    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'INSERT INTO %1$s (%2$s) VALUES (%3$s)',
        self::SPECIFICATION_SELECT => 'INSERT INTO %1$s %2$s %3$s',
    );

    /**
     * @var string|TableIdentifier
     */
    protected $table            = null;
    protected $columns          = array();

    /**
     * @var array|Select
     */
    protected $values           = null;

    /**
     * Constructor
     *
     * @param  null|string|TableIdentifier $table
     */
    public function __construct($table = null)
    {
        if ($table) {
            $this->into($table);
        }
    }

    /**
     * Create INTO clause
     *
     * @param  string|TableIdentifier $table
     * @return Insert
     */
    public function into($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Specify columns
     *
     * @param  array $columns
     * @return Insert
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Specify values to insert
     *
     * @param  array|Select $values
     * @param  string $flag one of VALUES_MERGE or VALUES_SET; defaults to VALUES_SET
     * @throws Exception\InvalidArgumentException
     * @return Insert
     */
    public function values($values, $flag = self::VALUES_SET)
    {
        if (!is_array($values) && !$values instanceof Select) {
            throw new Exception\InvalidArgumentException('values() expects an array of values or Zend\Db\Sql\Select instance');
        }

        if ($values instanceof Select) {
            if ($flag == self::VALUES_MERGE && (is_array($this->values) && !empty($this->values))) {
                throw new Exception\InvalidArgumentException(
                    'A Zend\Db\Sql\Select instance cannot be provided with the merge flag when values already exist.'
                );
            }
            $this->values = $values;
            return $this;
        }

        // determine if this is assoc or a set of values
        $keys = array_keys($values);
        $firstKey = current($keys);

        if ($flag == self::VALUES_SET) {
            $this->columns = array();
            $this->values = array();
        } elseif ($this->values instanceof Select) {
            throw new Exception\InvalidArgumentException(
                'An array of values cannot be provided with the merge flag when a Zend\Db\Sql\Select'
                . ' instance already exists as the value source.'
            );
        }

        if (is_string($firstKey)) {
            foreach ($keys as $key) {
                if (($index = array_search($key, $this->columns)) !== false) {
                    $this->values[$index] = $values[$key];
                } else {
                    $this->columns[] = $key;
                    $this->values[] = $values[$key];
                }
            }
        } elseif (is_int($firstKey)) {
            // determine if count of columns should match count of values
            $this->values = array_merge($this->values, array_values($values));
        }

        return $this;
    }

    /**
     * Create INTO SELECT clause
     *
     * @param Select $select
     * @return self
     */
    public function select(Select $select)
    {
        return $this->values($select);
    }

    /**
     * Get raw state
     *
     * @param string $key
     * @return mixed
     */
    public function getRawState($key = null)
    {
        $rawState = array(
            'table' => $this->table,
            'columns' => $this->columns,
            'values' => $this->values
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Prepare statement
     *
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statementContainer->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }

        $table = $this->table;
        $schema = null;

        // create quoted table name to use in insert processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        $table = $platform->quoteIdentifier($table);

        if ($schema) {
            $table = $platform->quoteIdentifier($schema) . $platform->getIdentifierSeparator() . $table;
        }

        $columns = array();
        $values  = array();

        if (is_array($this->values)) {
            foreach ($this->columns as $cIndex => $column) {
                $columns[$cIndex] = $platform->quoteIdentifier($column);
                if (isset($this->values[$cIndex]) && $this->values[$cIndex] instanceof Expression) {
                    $exprData = $this->processExpression($this->values[$cIndex], $platform, $driver);
                    $values[$cIndex] = $exprData->getSql();
                    $parameterContainer->merge($exprData->getParameterContainer());
                } else {
                    $values[$cIndex] = $driver->formatParameterName($column);
                    if (isset($this->values[$cIndex])) {
                        $parameterContainer->offsetSet($column, $this->values[$cIndex]);
                    } else {
                        $parameterContainer->offsetSet($column, null);
                    }
                }
            }
            $sql = sprintf(
                $this->specifications[static::SPECIFICATION_INSERT],
                $table,
                implode(', ', $columns),
                implode(', ', $values)
            );
        } elseif ($this->values instanceof Select) {
            $this->values->prepareStatement($adapter, $statementContainer);

            $columns = array_map(array($platform, 'quoteIdentifier'), $this->columns);
            $columns = implode(', ', $columns);

            $sql = sprintf(
                $this->specifications[static::SPECIFICATION_SELECT],
                $table,
                $columns ? "($columns)" : "",
                $statementContainer->getSql()
            );
        } else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }
        $statementContainer->setSql($sql);
    }

    /**
     * Get SQL string for this statement
     *
     * @param  null|PlatformInterface $adapterPlatform Defaults to Sql92 if none provided
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        $adapterPlatform = ($adapterPlatform) ?: new Sql92;
        $table = $this->table;
        $schema = null;

        // create quoted table name to use in insert processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        $table = $adapterPlatform->quoteIdentifier($table);

        if ($schema) {
            $table = $adapterPlatform->quoteIdentifier($schema) . $adapterPlatform->getIdentifierSeparator() . $table;
        }

        $columns = array_map(array($adapterPlatform, 'quoteIdentifier'), $this->columns);
        $columns = implode(', ', $columns);

        if (is_array($this->values)) {
            $values = array();
            foreach ($this->values as $value) {
                if ($value instanceof Expression) {
                    $exprData = $this->processExpression($value, $adapterPlatform);
                    $values[] = $exprData->getSql();
                } elseif ($value === null) {
                    $values[] = 'NULL';
                } else {
                    $values[] = $adapterPlatform->quoteValue($value);
                }
            }
            return sprintf(
                $this->specifications[static::SPECIFICATION_INSERT],
                $table,
                $columns,
                implode(', ', $values)
            );
        } elseif ($this->values instanceof Select) {
            $selectString = $this->values->getSqlString($adapterPlatform);
            if ($columns) {
                $columns = "($columns)";
            }
            return sprintf(
                $this->specifications[static::SPECIFICATION_SELECT],
                $table,
                $columns,
                $selectString
            );
        } else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }
    }

    /**
     * Overloading: variable setting
     *
     * Proxies to values, using VALUES_MERGE strategy
     *
     * @param  string $name
     * @param  mixed $value
     * @return Insert
     */
    public function __set($name, $value)
    {
        $values = array($name => $value);
        $this->values($values, self::VALUES_MERGE);
        return $this;
    }

    /**
     * Overloading: variable unset
     *
     * Proxies to values and columns
     *
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function __unset($name)
    {
        if (($position = array_search($name, $this->columns)) === false) {
            throw new Exception\InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }

        unset($this->columns[$position]);
        if (is_array($this->values)) {
            unset($this->values[$position]);
        }
    }

    /**
     * Overloading: variable isset
     *
     * Proxies to columns; does a column of that name exist?
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return in_array($name, $this->columns);
    }

    /**
     * Overloading: variable retrieval
     *
     * Retrieves value by column name
     *
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    public function __get($name)
    {
        if (!is_array($this->values)) {
            return null;
        }
        if (($position = array_search($name, $this->columns)) === false) {
            throw new Exception\InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }
        return $this->values[$position];
    }
}
