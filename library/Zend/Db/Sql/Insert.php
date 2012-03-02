<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Insert implements SqlInterface, PreparableSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    protected $specification    = 'INSERT INTO %1$s (%2$s) VALUES (%3$s)';
    protected $table            = null;
    protected $databaseOrSchema = null;
    protected $columns          = array();
    protected $values           = array();

    /**
     * Constructor
     * 
     * @param  null|string $table 
     * @param  null|string $databaseOrSchema 
     * @return void
     */
    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->into($table, $databaseOrSchema);
        }
    }

    /**
     * Crete INTO clause
     * 
     * @param  string $table 
     * @param  null|string $databaseOrSchema 
     * @return Insert
     */
    public function into($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        if ($databaseOrSchema) {
            $this->databaseOrSchema = $databaseOrSchema;
        }
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


    /**
     * Prepare statement
     *
     * @param  Adapter $adapter
     * @param  StatementInterface $statement
     * @return void
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

        $columns = array();
        $values  = array();

        foreach ($this->columns as $cIndex => $column) {
            $columns[$cIndex] = $platform->quoteIdentifier($column);
            if ($prepareType == 'positional') {
                $parameterContainer->offsetSet(null, $this->values[$cIndex]);
                $values[$cIndex] = $driver->formatParameterName(null);
            } elseif ($prepareType == 'named') {
                $values[$cIndex] = $driver->formatParameterName($column);
                $parameterContainer->offsetSet($column, $this->values[$cIndex]);
            }
        }

        $sql = sprintf($this->specification, $table, implode(', ', $columns), implode(', ', $values));

        $statement->setSql($sql);
    }

    /**
     * Get SQL string for this statement
     * 
     * @param  null|PlatformInterface $platform Defaults to Sql92 if none provided
     * @return string
     */
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
