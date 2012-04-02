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
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\ParameterContainerInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @property Where $where
 */
class Select extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    /**#@+
     * Constant
     * @const
     */
    const SPECIFICATION_SELECT = 'select';
    const SPECIFICATION_JOIN = 'join';
    const SPECIFICATION_WHERE = 'where';
    const SPECIFICATION_ORDER = 'order';
    const SPECIFICATION_FETCH = 'fetch';
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const SQL_WILDCARD = '*';
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESENDING = 'DESC';
    /**#@-*/

    /**
     * @var array Specifications
     */
    protected $specifications = array(
        self::SPECIFICATION_SELECT => 'SELECT %1$s FROM %2$s',
        self::SPECIFICATION_JOIN   => '%1$s JOIN %2$s ON %3$s',
        self::SPECIFICATION_WHERE  => 'WHERE %1$s',
        self::SPECIFICATION_ORDER  => 'ORDER BY %1$s',
        self::SPECIFICATION_FETCH  => 'FETCH FIRST %1$s'
    );

    /**
     * @var bool
     */
    protected $tableReadOnly = false;

    /**
     * @var bool
     */
    protected $prefixColumnsWithTable = true;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $schema = null;

    /**
     * @var array
     */
    protected $columns = array(self::SQL_WILDCARD);

    /**
     * @var array
     */
    protected $joins = array();

    /**
     * @var Where
     */
    protected $where = null;

    /**
     * @var null|string
     */
    protected $order = null;

    /**
     * @var int|null
     */
    protected $fetchNumber = null;

    /**
     * @var int|null
     */
    protected $fetchOffset = null;


    /**
     * Constructor
     * 
     * @param  null|string $table 
     * @param  null|string $schema
     * @return void
     */
    public function __construct($table = null, $schema = null)
    {
        if ($table) {
            $this->from($table, $schema);
            $this->tableReadOnly = true;
        }

        $this->where = new Where;
    }

    /**
     * Create from clause
     * 
     * @param  string $table 
     * @param  null|string $schema
     * @return Select
     */
    public function from($table, $schema = null)
    {
        if ($this->tableReadOnly) {
            throw new \InvalidArgumentException('Since this object was created with a table and/or schema in the constructor, it is read only.');
        }

        $this->table = $table;
        $this->schema = $schema;
        return $this;
    }

    /**
     * Specify columns from which to select
     *
     * Possible valid states:
     *
     *   array(*)
     *
     *   array(value, ...)
     *     value can be strings or Expression objects
     *
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     *
     * @param  array $columns
     * @return Select
     */
    public function columns(array $columns, $prefixColumnsWithTable = true)
    {
        $this->columns = $columns;
        $this->prefixColumnsWithTable = (bool) $prefixColumnsWithTable;
        return $this;
    }

    /**
     * Create join clause
     * 
     * @param  string $name 
     * @param  string $on 
     * @param  string|array $columns 
     * @param  string $type one of the JOIN_* constants
     * @return Select
     */
    public function join($name, $on, $columns = self::SQL_WILDCARD, $type = self::JOIN_INNER)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->joins[] = array(
            'name'    => $name,
            'on'      => $on,
            'columns' => $columns,
            'type'    => $type
        );
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
                $predicate = new Predicate\Expression($predicate);
                $this->where->addPredicate($predicate, $combination);
            } elseif (is_array($predicate)) {
                foreach ($predicate as $pkey => $pvalue) {
                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        $predicate = new Predicate\Expression($pkey, $pvalue);
                    } elseif (is_string($pkey)) {
                        $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                    } else {
                        $predicate = new Predicate\Expression($pvalue);
                    }
                    $this->where->addPredicate($predicate, $combination);
                }
            }
        }
        return $this;
    }

    /**
     * $order can be an array of:
     *
     * @todo
     *
     *
     * @param string|array $order
     * @return Select
     */
    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function fetch($number, $offset = null)
    {
        $this->fetchNumber = $number;
        $this->fetchOffset = $offset;
    }

    public function setSpecification($index, $specification)
    {
        $validSpecs = array(
            self::SPECIFICATION_SELECT,
            self::SPECIFICATION_JOIN,
            self::SPECIFICATION_WHERE,
            self::SPECIFICATION_ORDER,
            self::SPECIFICATION_FETCH
        );
        if (!in_array($index, $validSpecs)) {
            throw new Exception\InvalidArgumentException('Not a valid index');
        }
        $this->specifications[$index] = $specification;
        return $this;
    }

    public function getRawState($key = null)
    {
        $rawState = array(
            'columns' => $this->columns,
            'table' => $this->table,
            'schema' => $this->schema,
            'joins' => $this->joins,
            'where' => $this->where,
            'order' => $this->order,
            'fetch' => array($this->fetchNumber, $this->fetchOffset)
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Prepare statement
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Adapter\Driver\StatementInterface $statement
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        // setup initial objects and variables
        $platform = $adapter->getPlatform();
        $separator = $platform->getIdentifierSeparator();
        $parameterContainer = $statement->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainerInterface) {
            $parameterContainer = new ParameterContainer();
            $statement->setParameterContainer($parameterContainer);
        }

        // create quoted table name to use in columns processing
        $quotedTable = ($this->prefixColumnsWithTable)
            ? $platform->quoteIdentifier($this->table) . $platform->getIdentifierSeparator()
            : '';

        // process columns
        $columns = array();
        foreach ($this->columns as $columnIndexOrAs => $column) {
            $columnSql = '';
            if ($column instanceof Expression) {
                $columnParts = $this->processExpression($column, $platform, $adapter->getDriver(), 'column');
                if (count($columnParts['parameters']) > 0) {
                    $parameterContainer->merge($columnParts['parameters']);
                }
                $columnSql .= $columnParts['sql'];
            } else {
                $columnSql .= $quotedTable . $platform->quoteIdentifierInFragment($column);
            }
            if (is_string($columnIndexOrAs)) {
                $columnSql .= ' AS ' . $platform->quoteIdentifier($columnIndexOrAs);
            }
            $columns[] = $columnSql;
        }

        // process table name
        $table = $platform->quoteIdentifier($this->table);
        if ($this->schema != '') {
            $schema = $platform->quoteIdentifier($this->schema) . $separator;
            $table = $schema . $table;
        }

        // process joins
        if ($this->joins) {
            $joinSpecArgArray = array();
            foreach ($this->joins as $j => $join) {
                $joinSpecArgArray[$j] = array();
                $joinSpecArgArray[$j][] = strtoupper($join['type']); // type
                $joinSpecArgArray[$j][] = $platform->quoteIdentifier($join['name']); // table
                $joinSpecArgArray[$j][] = $platform->quoteIdentifierInFragment($join['on'], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join['columns'] as $jColumn) {
                    $columns[] = $joinSpecArgArray[$j][1] . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
            }
        }

        // create column name string
        $columns = implode(', ', $columns);

        // SQL Spec part 1: SELECT ... FROM ...
        $sql = sprintf($this->specifications[self::SPECIFICATION_SELECT], $columns, $table);

        // SQL Spect part 2: JOIN ...
        if (isset($joinSpecArgArray)) {
            foreach ($joinSpecArgArray as $joinSpecArgs) {
                $sql .= ' ' . vsprintf($this->specifications[self::SPECIFICATION_JOIN], $joinSpecArgs);
            }
        }

        // process where
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $platform, $adapter->getDriver(), 'where');
            if (count($whereParts['parameters']) > 0) {
                $parameterContainer->merge($whereParts['parameters']);
            }
            $sql .= ' ' . sprintf($this->specifications[self::SPECIFICATION_WHERE], $whereParts['sql']);
        }

        if (is_string($this->order) && $this->order != '') {
            $sql .= $this->applySpecification(self::SPECIFICATION_ORDER, $this->order);
        }

        // @todo Order and Fetch/Limit in prepare for Sql object
        $order = null;
        $limit = null;

        $sql .= (isset($limit)) ? sprintf($this->specifications[self::SPECIFICATION_FETCH], $limit) : '';

        $statement->setSql($sql);
    }

    /**
     * Get SQL string for statement
     *
     * @param  null|PlatformInterface $platform If null, defaults to Sql92
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // get platform, or create default
        $platform = ($platform) ?: new Sql92;

        // get identifier separator
        $separator = $platform->getIdentifierSeparator();

        // create quoted table name to use in columns processing
        $quotedTable = ($this->prefixColumnsWithTable)
            ? $platform->quoteIdentifier($this->table) . $platform->getIdentifierSeparator()
            : '';

        // process columns
        $columns = array();
        foreach ($this->columns as $columnIndexOrAs => $column) {
            $columnSql = '';
            if ($column instanceof Expression) {
                $columnParts = $this->processExpression($column, $platform, null, 'column');
                $columnSql .= $columnParts['sql'];
            } else {
                $columnSql .= $quotedTable . $platform->quoteIdentifierInFragment($column);
            }
            if (is_string($columnIndexOrAs)) {
                $columnSql .= ' AS ' . $platform->quoteIdentifier($columnIndexOrAs);
            }
            $columns[] = $columnSql;
        }

        // process the schema and table name
        $table = $platform->quoteIdentifier($this->table);
        if ($this->schema != '') {
            $schema = $platform->quoteIdentifier($this->schema) . $platform->getIdentifierSeparator();
            $table = $schema . $table;
        } else {
            $schema = '';
        }

        // process joins
        if ($this->joins) {
            $joinSpecArgArray = array();
            foreach ($this->joins as $j => $join) {
                $joinSpecArgArray[$j] = array();
                $joinSpecArgArray[$j][] = strtoupper($join['type']); // type
                $joinSpecArgArray[$j][] = $platform->quoteIdentifier($join['name']); // table
                $joinSpecArgArray[$j][] = $platform->quoteIdentifierInFragment($join['on'], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join['columns'] as /* $jColumnKey => */ $jColumn) {
                    $columns[] = $joinSpecArgArray[$j][1] . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
            }
        }

        // convert columns to string
        $columns = implode(', ', $columns);

        // create sql
        $sql = sprintf($this->specifications[self::SPECIFICATION_SELECT], $columns, $table);

        // add in joins
        if (isset($joinSpecArgArray)) {
            foreach ($joinSpecArgArray as $joinSpecArgs) {
                $sql .= ' ' . vsprintf($this->specifications[self::SPECIFICATION_JOIN], $joinSpecArgs);
            }
        }

        // process where
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $platform, null, 'where');
            $sql .= ' ' . sprintf($this->specifications[self::SPECIFICATION_WHERE], $whereParts['sql']);
        }

        // process order & limit
        // @todo this is too basic, but good for now
        //$sql .= (isset($this->order)) ? sprintf($this->specifications[self::SPECIFICATION_ORDER], $this->order) : '';
        //$sql .= (isset($this->limit)) ? sprintf($this->specifications[self::SPECIFICATION_FETCH], $this->limit) : '';

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
            default:
                throw new Exception\InvalidArgumentException('Not a valid magic property for this object');
        }
    }

    /**
     * __clone 
     *
     * Resets the where object each time the Select is cloned.
     * 
     * @return void
     */
    public function __clone() {
        $this->where = clone $this->where;
    }
}
