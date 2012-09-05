<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Platform\Sql92;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
class Insert extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    /**#@+
     * Constants
     *
     * @const
     */
    const SPECIFICATION_INSERT = 'insert';
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';
    /**#@-*/

    /**
     * @var array Specification array
     */
    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'INSERT INTO %1$s (%2$s) VALUES (%3$s)'
    );

    /**
     * @var string
     */
    protected $table            = null;
    protected $columns          = array();

    /**
     * @var array
     */
    protected $values           = array();

    /**
     * Constructor
     *
     * @param  null|string $table
     */
    public function __construct($table = null)
    {
        if ($table) {
            $this->into($table);
        }
    }

    /**
     * Crete INTO clause
     *
     * @param  string $table
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
     * @param  array $values
     * @param  string $flag one of VALUES_MERGE or VALUES_SET; defaults to VALUES_SET
     * @return Insert
     */
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
     * @param  Adapter $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementContainerInterface $statementContainer)
    {
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statementContainer->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }

        $table = $platform->quoteIdentifier($this->table);

        $columns = array();
        $values  = array();

        foreach ($this->columns as $cIndex => $column) {
            $columns[$cIndex] = $platform->quoteIdentifier($column);
            if ($this->values[$cIndex] instanceof Expression) {
                $exprData = $this->processExpression($this->values[$cIndex], $platform, $adapter);
                $values[$cIndex] = $exprData->getSql();
                $parameterContainer->merge($exprData->getParameterContainer());
            } else {
                $values[$cIndex] = $driver->formatParameterName($column);
                $parameterContainer->offsetSet($column, $this->values[$cIndex]);
            }
        }

        $sql = sprintf(
            $this->specifications[self::SPECIFICATION_INSERT],
            $table,
            implode(', ', $columns),
            implode(', ', $values)
        );

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
        $table = $adapterPlatform->quoteIdentifier($this->table);

        $columns = array_map(array($adapterPlatform, 'quoteIdentifier'), $this->columns);
        $columns = implode(', ', $columns);

        $values = array();
        foreach ($this->values as $value) {
            if ($value instanceof Expression) {
                $exprData = $this->processExpression($value, $adapterPlatform);
                $values[] = $exprData->getSql();
            } elseif (is_null($value)) {
                $values[] = 'NULL';
            } else {
                $values[] = $adapterPlatform->quoteValue($value);
            }
        }

        $values = implode(', ', $values);

        return sprintf($this->specifications[self::SPECIFICATION_INSERT], $table, $columns, $values);
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
     * @return void
     */
    public function __unset($name)
    {
        if (($position = array_search($name, $this->columns)) === false) {
            throw new \InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }

        unset($this->columns[$position]);
        unset($this->values[$position]);
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
     * @return mixed
     */
    public function __get($name)
    {
        if (($position = array_search($name, $this->columns)) === false) {
            throw new \InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }
        return $this->values[$position];
    }
}
