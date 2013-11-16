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
use Zend\Stdlib\PriorityList;

/**
 *
 * @property Where $where
 */
class Update extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    /**@#++
     * @const
     */
    const SPECIFICATION_UPDATE = 'update';
    const SPECIFICATION_WHERE = 'where';

    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';
    /**@#-**/

    protected $specifications = array(
        self::SPECIFICATION_UPDATE => 'UPDATE %1$s SET %2$s',
        self::SPECIFICATION_WHERE => 'WHERE %1$s'
    );

    /**
     * @var string|TableIdentifier
     */
    protected $table = '';

    /**
     * @var bool
     */
    protected $emptyWhereProtection = true;

    /**
     * @var PriorityList
     */
    protected $set;

    /**
     * @var string|Where
     */
    protected $where = null;

    /**
     * Constructor
     *
     * @param  null|string|TableIdentifier $table
     */
    public function __construct($table = null)
    {
        if ($table) {
            $this->table($table);
        }
        $this->where = new Where();
        $this->set = new PriorityList();
        $this->set->isLIFO(false);
    }

    /**
     * Specify table for statement
     *
     * @param  string|TableIdentifier $table
     * @return Update
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set key/value pairs to update
     *
     * @param  array $values Associative array of key values
     * @param  string $flag One of the VALUES_* constants
     * @throws Exception\InvalidArgumentException
     * @return Update
     */
    public function set(array $values, $flag = self::VALUES_SET)
    {
        if ($values == null) {
            throw new Exception\InvalidArgumentException('set() expects an array of values');
        }

        if ($flag == self::VALUES_SET) {
            $this->set->clear();
        }
        $priority = is_numeric($flag) ? $flag : 0;
        foreach ($values as $k => $v) {
            if (!is_string($k)) {
                throw new Exception\InvalidArgumentException('set() expects a string for the value key');
            }
            $this->set->insert($k, $v, $priority);
        }
        return $this;
    }

    /**
     * Create where clause
     *
     * @param  Where|\Closure|string|array $predicate
     * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
     * @throws Exception\InvalidArgumentException
     * @return Select
     */
    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND)
    {
        if ($predicate instanceof Where) {
            $this->where = $predicate;
        } else {
            $this->where->addPredicates($predicate, $combination);
        }
        return $this;
    }

    public function getRawState($key = null)
    {
        $rawState = array(
            'emptyWhereProtection' => $this->emptyWhereProtection,
            'table' => $this->table,
            'set' => $this->set->toArray(),
            'where' => $this->where
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Prepare statement
     *
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
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

        // create quoted table name to use in update processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        $table = $platform->quoteIdentifier($table);

        if ($schema) {
            $table = $platform->quoteIdentifier($schema) . $platform->getIdentifierSeparator() . $table;
        }

        $setSql = array();
        foreach ($this->set as $column => $value) {
            if ($value instanceof Expression) {
                $exprData = $this->processExpression($value, $platform, $driver);
                $setSql[] = $platform->quoteIdentifier($column) . ' = ' . $exprData->getSql();
                $parameterContainer->merge($exprData->getParameterContainer());
            } else {
                $setSql[] = $platform->quoteIdentifier($column) . ' = ' . $driver->formatParameterName($column);
                $parameterContainer->offsetSet($column, $value);
            }
        }
        $set = implode(', ', $setSql);

        $sql = sprintf($this->specifications[static::SPECIFICATION_UPDATE], $table, $set);

        // process where
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $platform, $driver, 'where');
            $parameterContainer->merge($whereParts->getParameterContainer());
            $sql .= ' ' . sprintf($this->specifications[static::SPECIFICATION_WHERE], $whereParts->getSql());
        }
        $statementContainer->setSql($sql);
    }

    /**
     * Get SQL string for statement
     *
     * @param  null|PlatformInterface $adapterPlatform If null, defaults to Sql92
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        $adapterPlatform = ($adapterPlatform) ?: new Sql92;
        $table = $this->table;
        $schema = null;

        // create quoted table name to use in update processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        $table = $adapterPlatform->quoteIdentifier($table);

        if ($schema) {
            $table = $adapterPlatform->quoteIdentifier($schema) . $adapterPlatform->getIdentifierSeparator() . $table;
        }

        $setSql = array();
        foreach ($this->set as $column => $value) {
            if ($value instanceof ExpressionInterface) {
                $exprData = $this->processExpression($value, $adapterPlatform);
                $setSql[] = $adapterPlatform->quoteIdentifier($column) . ' = ' . $exprData->getSql();
            } elseif ($value === null) {
                $setSql[] = $adapterPlatform->quoteIdentifier($column) . ' = NULL';
            } else {
                $setSql[] = $adapterPlatform->quoteIdentifier($column) . ' = ' . $adapterPlatform->quoteValue($value);
            }
        }
        $set = implode(', ', $setSql);

        $sql = sprintf($this->specifications[static::SPECIFICATION_UPDATE], $table, $set);
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $adapterPlatform, null, 'where');
            $sql .= ' ' . sprintf($this->specifications[static::SPECIFICATION_WHERE], $whereParts->getSql());
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

    /**
     * __clone
     *
     * Resets the where object each time the Update is cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $this->where = clone $this->where;
        $this->set = clone $this->set;
    }
}
