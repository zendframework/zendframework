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
 *
 * @property Where $where
 */
class Update implements SqlInterface, PreparableSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    protected $specification        = 'UPDATE %1$s SET %2$s';
    protected $databaseOrSchema     = null;
    protected $table                = null;
    protected $emptyWhereProtection = true;
    protected $set                  = array();
    protected $where                = null;

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
            $this->table($table, $databaseOrSchema);
        }
        $this->where = new Where();
    }

    /**
     * Specify table for statement
     * 
     * @param  string $table 
     * @param  null|string $databaseOrSchema 
     * @return Update
     */
    public function table($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        if ($databaseOrSchema) {
            $this->databaseOrSchema = $databaseOrSchema;
        }
        return $this;
    }

    /**
     * Set key/value pairs to update
     * 
     * @param  array $values Associative array of key values
     * @param  string $flag One of the VALUES_* constants
     * @return Update
     */
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

    /**
     * Create where clause
     * 
     * @param  Where|\Closure|string|array $predicate 
     * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
     * @return Select
     */
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

    /**
     * Prepare statement
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Adapter\Driver\StatementInterface $statement
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

    /**
     * Get SQL string for statement
     * 
     * @param  null|PlatformInterface $platform If null, defaults to Sql92
     * @return string
     */
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
                $setSql[] = $platform->quoteIdentifier($setName) . ' = ' . $platform->quoteValue($setValue);
            }
            $set = implode(', ', $setSql);
        }

        $sql = sprintf($this->specification, $table, $set);
        if ($this->where->count() > 0) {
            $sql .= $this->where->getSqlString($platform);
        }
        return $sql;
    }

    /**
     * Variable overloading
     *
     * Proxies to "where" only
     * 
     * @param  string $name 
     * @return mixed
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'where':
                return $this->where;
        }
    }
}
