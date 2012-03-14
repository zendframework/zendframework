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
class Select implements SqlInterface, PreparableSqlInterface
{
    const SPECIFICATION_SELECT = 0;
    const SPECIFICATION_JOIN = 1;
    const SPECIFICATION_ORDER = 2;
    const SPECIFICATION_FETCH = 3;

    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const SQL_WILDCARD = '*';

    protected $specifications = array(
        self::SPECIFICATION_SELECT => 'SELECT %1$s FROM %2$s',
        self::SPECIFICATION_JOIN   => '%1$s JOIN %2$s ON %3$s',
        self::SPECIFICATION_ORDER  => 'ORDER BY %1$s',
        self::SPECIFICATION_FETCH  => 'FETCH %1$s'
    );

    protected $columns = array(self::SQL_WILDCARD);

    protected $table = null;
    protected $schema = null;

    protected $joins = array();

    protected $where = null;
    protected $order = null;
    protected $limit = null;

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
        $this->table = $table;
        $this->schema = $schema;
        return $this;
    }

    /**
     * Specify columns from which to select
     * 
     * @param  array $columns 
     * @return Select
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /*
     * @todo do Union
     * @throws \RuntimeException Unimplemented
    public function union($select = array(), $type = self::SQL_UNION)
    {
        throw new \RuntimeException(sprintf('%s is not yet implemented'), __METHOD__);
    }
     */

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
            } elseif (is_array($predicate)) {
                foreach ($predicate as $pkey => $pvalue) {
                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        $predicate = new Predicate\Expression($pkey, $pvalue);
                    } elseif (is_string($pkey)) {
                        $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                    } else {
                        $predicate = new Predicate\Expression($pvalue);
                    }
                }
            }
            $this->where->addPredicate($predicate, $combination);
        }
        return $this;
    }

    public function setSpecification($index, $specification)
    {
        if (!in_array($index, array(self::SPECIFICATION_SELECT, self::SPECIFICATION_JOIN, self::SPECIFICATION_ORDER, self::SPECIFICATION_FETCH))) {
            throw new Exception\InvalidArgumentException('Not a valid index');
        }
        $this->specifications[$index] = $specification;
        return $this;
    }

    public function getRawState()
    {
        return array(
            'columns' => $this->columns,
            'table' => $this->table,
            'schema' => $this->schema,
            'joins' => $this->joins,
            'where' => $this->where,
            'order' => $this->order,
            'limit' => $this->limit
        );
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
        $platform = $adapter->getPlatform();
        $separator = $platform->getIdentifierSeparator();

        $columns = array();
        foreach ($this->columns as $columnKey => $column) {
            //$columns[] = $platform->quoteIdentifierInFragment($column);

            if (is_string($columnKey)) {
                $columns[] = $columnKey . ' AS ' . $platform->quoteIdentifierInFragment($column);
            } else {
                $columns[] = $platform->quoteIdentifierInFragment($column);
            }

        }

        $table = $platform->quoteIdentifier($this->table);
        if ($this->schema != '') {
            $schema = $platform->quoteIdentifier($this->schema) . $platform->getIdentifierSeparator();
            $table = $schema . $table;
        } else {
            $schema = '';
        }

        if ($this->joins) {
            $joinSpecArgArray = array();
            foreach ($this->joins as $j => $join) {
                $joinSpecArgArray[$j] = array();
                $joinSpecArgArray[$j][] = strtoupper($join['type']); // type
                $joinSpecArgArray[$j][] = $platform->quoteIdentifier($join['name']); // table
                $joinSpecArgArray[$j][] = $platform->quoteIdentifierInFragment($join['on'], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join['columns'] as /* $jColumnKey => */ $jColumn) {
                    $columns[] = $joinSpecArgArray[$j][3] . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
            }
        }


        $columns = implode(', ', $columns);

        $sql = sprintf($this->specifications[self::SPECIFICATION_SELECT], $columns, $table);

        if (isset($joinSpecArgArray)) {
            foreach ($joinSpecArgArray as $joinSpecArgs) {
                $sql .= ' ' . vsprintf($this->specifications[self::SPECIFICATION_JOIN], $joinSpecArgs);
            }
        }

        if ($this->where->count() > 0) {
            $statement->setSql($sql);
            $this->where->prepareStatement($adapter, $statement);
            $sql = $statement->getSql();
        }
        $limit = null; // @todo

        $sql .= (isset($order)) ? sprintf($this->specifications[self::SPECIFICATION_ORDER], $order) : '';
        $sql .= (isset($limit)) ? sprintf($this->specifications[self::SPECIFICATION_FETCH], $limit) : '';


        $order = null; // @todo
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

        // process column names (@todo currently simple array only)
        $columns = array();
        foreach ($this->columns as $columnKey => $column) {
            $columns[] = $platform->quoteIdentifierInFragment($column);
        }

        // process the schema and table name
        $table = $platform->quoteIdentifier($this->table);
        if ($this->schema != '') {
            $schema = $platform->quoteIdentifier($this->schema) . $platform->getIdentifierSeparator();
            $table = $schema . $table;
        } else {
            $schema = '';
        }

        // process any joins
        if ($this->joins) {
            $joinSpecArgArray = array();
            foreach ($this->joins as $j => $join) {
                $joinSpecArgArray[$j] = array();
                $joinSpecArgArray[$j][] = strtoupper($join['type']); // type
                $joinSpecArgArray[$j][] = $platform->quoteIdentifier($join['name']); // table
                $joinSpecArgArray[$j][] = $platform->quoteIdentifierInFragment($join['on'], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join['columns'] as $jColumnKey => $jColumn) {
                    $columns[] = $joinSpecArgArray[$j][3] . $separator . $platform->quoteIdentifierInFragment($jColumn);
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

        // add in where if it exists
        if ($this->where->count() > 0) {
            $sql .= $this->where->getSqlString($platform);
        }

        // process order & limit (@todo this is too basic, but good for now)
        $sql .= (isset($this->order)) ? sprintf($this->specifications[self::SPECIFICATION_ORDER], $this->order) : '';
        $sql .= (isset($this->limit)) ? sprintf($this->specifications[self::SPECIFICATION_FETCH], $this->limit) : '';
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
//            case 'table':
//                return $this->table;
//            case 'schema':
//                return $this->schema;
//            case 'joins':
//                return $this->joins;
//            case 'columns':
//                return $this->columns;
//            case '':
//                return $this->
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
