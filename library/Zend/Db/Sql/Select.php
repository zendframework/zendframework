<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 *
 * @property Where $where
 * @property Having $having
 */
class Select extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    /**#@+
     * Constant
     * @const
     */
    const SELECT = 'select';
    const COLUMNS = 'columns';
    const TABLE = 'table';
    const JOINS = 'joins';
    const WHERE = 'where';
    const GROUP = 'group';
    const HAVING = 'having';
    const ORDER = 'order';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const SQL_STAR = '*';
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';
    /**#@-*/

    /**
     * @var array Specifications
     */
    protected $specifications = array(
        self::SELECT => array(
            'SELECT %1$s FROM %2$s' => array(
                array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),
                null
            )
        ),
        self::JOINS  => array(
            '%1$s' => array(
                array(3 => '%1$s JOIN %2$s ON %3$s', 'combinedby' => ' ')
            )
        ),
        self::WHERE  => 'WHERE %1$s',
        self::GROUP  => array(
            'GROUP BY %1$s' => array(
                array(1 => '%1$s', 'combinedby' => ', ')
            )
        ),
        self::HAVING => 'HAVING %1$s',
        self::ORDER  => array(
            'ORDER BY %1$s' => array(
                array(1 => '%1$s', 2 => '%1$s %2$s', 'combinedby' => ', ')
            )
        ),
        self::LIMIT  => 'LIMIT %1$s',
        self::OFFSET => 'OFFSET %1$s'
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
     * @var string|TableIdentifier
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $columns = array(self::SQL_STAR);

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
    protected $order = array();

    /**
     * @var null|array
     */
    protected $group = null;

    /**
     * @var null|string|array
     */
    protected $having = null;

    /**
     * @var int|null
     */
    protected $limit = null;

    /**
     * @var int|null
     */
    protected $offset = null;

    /**
     * Constructor
     *
     * @param  null|string $table
     */
    public function __construct($table = null)
    {
        if ($table) {
            $this->from($table);
            $this->tableReadOnly = true;
        }

        $this->where = new Where;
        $this->having = new Having;
    }

    /**
     * Create from clause
     *
     * @param  string|array|TableIdentifier $table
     * @throws Exception\InvalidArgumentException
     * @return Select
     */
    public function from($table)
    {
        if ($this->tableReadOnly) {
            throw new Exception\InvalidArgumentException('Since this object was created with a table and/or schema in the constructor, it is read only.');
        }

        if (!is_string($table) && !is_array($table) && !$table instanceof TableIdentifier) {
            throw new Exception\InvalidArgumentException('$table must be a string, array, or an instance of TableIdentifier');
        }

        if (is_array($table) && (!is_string(key($table)) || count($table) !== 1)) {
            throw new Exception\InvalidArgumentException('from() expects $table as an array is a single element associative array');
        }

        $this->table = $table;
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
     * @param  bool  $prefixColumnsWithTable
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
     * @param  string|array $name
     * @param  string $on
     * @param  string|array $columns
     * @param  string $type one of the JOIN_* constants
     * @throws Exception\InvalidArgumentException
     * @return Select
     */
    public function join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER)
    {
        if (is_array($name) && (!is_string(key($name)) || count($name) !== 1)) {
            throw new Exception\InvalidArgumentException(
                sprintf("join() expects '%s' as an array is a single element associative array", array_shift($name))
            );
        }
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
                // String $predicate should be passed as an expression
                $predicate = new Predicate\Expression($predicate);
                $this->where->addPredicate($predicate, $combination);
            } elseif (is_array($predicate)) {

                foreach ($predicate as $pkey => $pvalue) {
                    // loop through predicates

                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        // First, process strings that the abstraction replacement character ?
                        // as an Expression predicate
                        $predicate = new Predicate\Expression($pkey, $pvalue);

                    } elseif (is_string($pkey)) {
                        // Otherwise, if still a string, do something intelligent with the PHP type provided

                        if ($pvalue === null) {
                            // map PHP null to SQL IS NULL expression
                            $predicate = new Predicate\IsNull($pkey, $pvalue);
                        } elseif (is_array($pvalue)) {
                            // if the value is an array, assume IN() is desired
                            $predicate = new Predicate\In($pkey, $pvalue);
                        } else {
                            // otherwise assume that array('foo' => 'bar') means "foo" = 'bar'
                            $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                        }
                    } elseif ($pvalue instanceof Predicate\PredicateInterface) {
                        // Predicate type is ok
                        $predicate = $pvalue;
                    } else {
                        // must be an array of expressions (with int-indexed array)
                        $predicate = new Predicate\Expression($pvalue);
                    }
                    $this->where->addPredicate($predicate, $combination);
                }
            }
        }
        return $this;
    }

    public function group($group)
    {
        if (is_array($group)) {
            foreach ($group as $o) {
                $this->group[] = $o;
            }
        } else {
            $this->group[] = $group;
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
    public function having($predicate, $combination = Predicate\PredicateSet::OP_AND)
    {
        if ($predicate instanceof Having) {
            $this->having = $predicate;
        } elseif ($predicate instanceof \Closure) {
            $predicate($this->having);
        } else {
            if (is_string($predicate)) {
                $predicate = new Predicate\Expression($predicate);
                $this->having->addPredicate($predicate, $combination);
            } elseif (is_array($predicate)) {
                foreach ($predicate as $pkey => $pvalue) {
                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        $predicate = new Predicate\Expression($pkey, $pvalue);
                    } elseif (is_string($pkey)) {
                        $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                    } else {
                        $predicate = new Predicate\Expression($pvalue);
                    }
                    $this->having->addPredicate($predicate, $combination);
                }
            }
        }
        return $this;
    }

    /**
     * @param string|array $order
     * @return Select
     */
    public function order($order)
    {
        if (is_string($order)) {
            if (strpos($order, ',') !== false) {
                $order = preg_split('#,\s+#', $order);
            } else {
                $order = (array) $order;
            }
        }
        foreach ($order as $k => $v) {
            if (is_string($k)) {
                $this->order[$k] = $v;
            } else {
                $this->order[] = $v;
            }
        }
        return $this;
    }

    /**
     * @param int $limit
     * @return Select
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return Select
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param string $part
     * @return Select
     * @throws Exception\InvalidArgumentException
     */
    public function reset($part)
    {
        switch ($part) {
            case self::TABLE:
                if ($this->tableReadOnly) {
                    throw new Exception\InvalidArgumentException(
                        'Since this object was created with a table and/or schema in the constructor, it is read only.'
                    );
                }
                $this->table = null;
                break;
            case self::COLUMNS:
                $this->columns = array();
                break;
            case self::JOINS:
                $this->joins = array();
                break;
            case self::WHERE:
                $this->where = new Where;
                break;
            case self::GROUP:
                $this->group = null;
                break;
            case self::HAVING:
                $this->having = new Having;
                break;
            case self::LIMIT:
                $this->limit = null;
                break;
            case self::OFFSET:
                $this->offset = null;
                break;
            case self::ORDER:
                $this->order = null;
                break;
        }
        return $this;
    }

    public function setSpecification($index, $specification)
    {
        if (!method_exists($this, 'process' . $index)) {
            throw new Exception\InvalidArgumentException('Not a valid specification name.');
        }
        $this->specifications[$index] = $specification;
        return $this;
    }

    public function getRawState($key = null)
    {
        $rawState = array(
            self::TABLE   => $this->table,
            self::COLUMNS => $this->columns,
            self::JOINS   => $this->joins,
            self::WHERE   => $this->where,
            self::ORDER   => $this->order,
            self::GROUP   => $this->group,
            self::HAVING  => $this->having,
            self::LIMIT   => $this->limit,
            self::OFFSET  => $this->offset
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Prepare statement
     *
     * @param Adapter $adapter
     * @param StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementContainerInterface $statementContainer)
    {
        // ensure statement has a ParameterContainer
        $parameterContainer = $statementContainer->getParameterContainer();
        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }

        $sqls = array();
        $parameters = array();
        $platform = $adapter->getPlatform();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($platform, $adapter, $parameterContainer, $sqls, $parameters);
            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
        }

        $sql = implode(' ', $sqls);

        $statementContainer->setSql($sql);
        return;
    }

    /**
     * Get SQL string for statement
     *
     * @param  null|PlatformInterface $adapterPlatform If null, defaults to Sql92
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($adapterPlatform, null, null, $sqls, $parameters);
            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
        }

        $sql = implode(' ', $sqls);
        return $sql;
    }

    protected function processSelect(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        $expr = 1;

        if (!$this->table) {
            return null;
        }

        $table = $this->table;
        $schema = $alias = null;

        if (is_array($table)) {
            $alias = key($this->table);
            $table = current($this->table);
        }

        // create quoted table name to use in columns processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        if ($table instanceof Select) {
            $table = '(' . $this->processSubselect($table, $platform, $adapter, $parameterContainer) . ')';
        } else {
            $table = $platform->quoteIdentifier($table);
        }

        if ($schema) {
            $table = $platform->quoteIdentifier($schema) . $platform->getIdentifierSeparator() . $table;
        }

        if ($alias) {
            $fromTable = $platform->quoteIdentifier($alias);
            $table .= ' AS ' . $fromTable;
        } else {
            $fromTable = ($this->prefixColumnsWithTable) ? $table : '';
        }

        $fromTable .= ($this->prefixColumnsWithTable) ? $platform->getIdentifierSeparator() : '';

        // process table columns
        $columns = array();
        foreach ($this->columns as $columnIndexOrAs => $column) {

            $columnName = '';
            if ($column === self::SQL_STAR) {
                $columns[] = array($fromTable . self::SQL_STAR);
                continue;
            }

            if ($column instanceof Expression) {
                $columnParts = $this->processExpression(
                    $column,
                    $platform,
                    $adapter,
                    $this->processInfo['paramPrefix'] . ((is_string($columnIndexOrAs)) ? $columnIndexOrAs : 'column')
                );
                if ($parameterContainer) {
                    $parameterContainer->merge($columnParts->getParameterContainer());
                }
                $columnName .= $columnParts->getSql();
            } else {
                $columnName .= $fromTable . $platform->quoteIdentifier($column);
            }

            // process As portion
            if (is_string($columnIndexOrAs)) {
                $columnAs = $platform->quoteIdentifier($columnIndexOrAs);
            } elseif (stripos($columnName, ' as ') === false) {
                $columnAs = (is_string($column)) ? $platform->quoteIdentifier($column) : 'Expression' . $expr++;
            }
            $columns[] = (isset($columnAs)) ? array($columnName, $columnAs) : array($columnName);
        }

        $separator = $platform->getIdentifierSeparator();

        // process join columns
        foreach ($this->joins as $join) {
            foreach ($join['columns'] as $jKey => $jColumn) {
                $jColumns = array();
                if ($jColumn instanceof ExpressionInterface) {
                    $jColumnParts = $this->processExpression(
                        $jColumn,
                        $platform,
                        $adapter,
                        $this->processInfo['paramPrefix'] . ((is_string($jKey)) ? $jKey : 'column')
                    );
                    if ($parameterContainer) {
                        $parameterContainer->merge($jColumnParts->getParameterContainer());
                    }
                    $jColumns[] = $jColumnParts->getSql();
                } else {
                    $name = (is_array($join['name'])) ? key($join['name']) : $name = $join['name'];
                    $jColumns[] = $platform->quoteIdentifier($name) . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
                if (is_string($jKey)) {
                    $jColumns[] = $platform->quoteIdentifier($jKey);
                } elseif ($jColumn !== self::SQL_STAR) {
                    $jColumns[] = $platform->quoteIdentifier($jColumn);
                }
                $columns[] = $jColumns;
            }
        }

        return array($columns, $table);
    }

    protected function processJoins(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if (!$this->joins) {
            return null;
        }

        // process joins
        $joinSpecArgArray = array();
        foreach ($this->joins as $j => $join) {
            $joinSpecArgArray[$j] = array();
            // type
            $joinSpecArgArray[$j][] = strtoupper($join['type']);
            // table name
            $joinSpecArgArray[$j][] = (is_array($join['name']))
                ? $platform->quoteIdentifier(current($join['name'])) . ' AS ' . $platform->quoteIdentifier(key($join['name']))
                : $platform->quoteIdentifier($join['name']);
            // on expression
            // note: for Expression objects, pass them to processExpression with a prefix specific to each join (used for named parameters)
            $joinSpecArgArray[$j][] = ($join['on'] instanceof ExpressionInterface)
                ? $this->processExpression($join['on'], $platform, $adapter, $this->processInfo['paramPrefix'] . 'join' . ($j+1) . 'part')
                : $platform->quoteIdentifierInFragment($join['on'], array('=', 'AND', 'OR', '(', ')', 'BETWEEN', '<', '>')); // on
            if ($joinSpecArgArray[$j][2] instanceof StatementContainerInterface) {
                if ($parameterContainer) {
                    $parameterContainer->merge($joinSpecArgArray[$j][2]->getParameterContainer());
                }
                $joinSpecArgArray[$j][2] = $joinSpecArgArray[$j][2]->getSql();
            }
        }

        return array($joinSpecArgArray);
    }

    protected function processWhere(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->where->count() == 0) {
            return null;
        }
        $whereParts = $this->processExpression($this->where, $platform, $adapter, $this->processInfo['paramPrefix'] . 'where');
        if ($parameterContainer) {
            $parameterContainer->merge($whereParts->getParameterContainer());
        }
        return array($whereParts->getSql());
    }

    protected function processGroup(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->group === null) {
            return null;
        }
        // process table columns
        $groups = array();
        foreach ($this->group as $column) {
            $columnSql = '';
            if ($column instanceof Expression) {
                $columnParts = $this->processExpression($column, $platform, $adapter, $this->processInfo['paramPrefix'] . 'group');
                if ($parameterContainer) {
                    $parameterContainer->merge($columnParts->getParameterContainer());
                }
                $columnSql .= $columnParts->getSql();
            } else {
                $columnSql .= $platform->quoteIdentifierInFragment($column);
            }
            $groups[] = $columnSql;
        }
        return array($groups);
    }

    protected function processHaving(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->having->count() == 0) {
            return null;
        }
        $whereParts = $this->processExpression($this->having, $platform, $adapter, $this->processInfo['paramPrefix'] . 'having');
        if ($parameterContainer) {
            $parameterContainer->merge($whereParts->getParameterContainer());
        }
        return array($whereParts->getSql());
    }

    protected function processOrder(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if (empty($this->order)) {
            return null;
        }
        $orders = array();
        foreach ($this->order as $k => $v) {
            if ($v instanceof Expression) {
                /** @var $orderParts \Zend\Db\Adapter\StatementContainer */
                $orderParts = $this->processExpression($v, $platform, $adapter);
                if ($parameterContainer) {
                    $parameterContainer->merge($orderParts->getParameterContainer());
                }
                $orders[] = array($orderParts->getSql());
                continue;
            }
            if (is_int($k)) {
                if (strpos($v, ' ') !== false) {
                    list($k, $v) = preg_split('# #', $v, 2);
                } else {
                    $k = $v;
                    $v = self::ORDER_ASCENDING;
                }
            }
            if (strtoupper($v) == self::ORDER_DESCENDING) {
                $orders[] = array($platform->quoteIdentifierInFragment($k), self::ORDER_DESCENDING);
            } else {
                $orders[] = array($platform->quoteIdentifierInFragment($k), self::ORDER_ASCENDING);
            }
        }
        return array($orders);
    }

    protected function processLimit(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->limit === null) {
            return null;
        }
        if ($adapter) {
            $driver = $adapter->getDriver();
            $sql = $driver->formatParameterName('limit');
            $parameterContainer->offsetSet('limit', $this->limit, ParameterContainer::TYPE_INTEGER);
        } else {
            $sql = $platform->quoteValue($this->limit);
        }

        return array($sql);
    }

    protected function processOffset(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->offset === null) {
            return null;
        }
        if ($adapter) {
            $parameterContainer->offsetSet('offset', $this->offset, ParameterContainer::TYPE_INTEGER);
            return array($adapter->getDriver()->formatParameterName('offset'));
        }

        return array($platform->quoteValue($this->offset));
    }

    /**
     * Variable overloading
     *
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'where':
                return $this->where;
            case 'having':
                return $this->having;
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
    public function __clone()
    {
        $this->where  = clone $this->where;
        $this->having = clone $this->having;
    }
}
