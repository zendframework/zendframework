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

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
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
     * @var string
     */
    protected $table = '';

    /**
     * @var bool
     */
    protected $emptyWhereProtection = true;

    /**
     * @var array
     */
    protected $set = array();

    /**
     * @var string|Where
     */
    protected $where = null;

    /**
     * Constructor
     * 
     * @param  null|string $table 
     * @param  null|string $schema
     * @return void
     */
    public function __construct($table = null)
    {
        if ($table) {
            $this->table($table);
        }
        $this->where = new Where();
    }

    /**
     * Specify table for statement
     * 
     * @param  string $table 
     * @param  null|string $schema
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

    public function getRawState($key = null)
    {
        $rawState = array(
            'emptyWhereProtection' => $this->emptyWhereProtection,
            'table' => $this->table,
            'set' => $this->set,
            'where' => $this->where
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
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statement->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statement->setParameterContainer($parameterContainer);
        }

        $table = $platform->quoteIdentifier($this->table);

        $set = $this->set;
        if (is_array($set)) {
            $setSql = array();
            foreach ($set as $column => $value) {
                $parameterContainer->offsetSet($column, $value);
                $setSql[] = $platform->quoteIdentifier($column) . ' = ' . $driver->formatParameterName($column);
            }
            $set = implode(', ', $setSql);
        }

        $sql = sprintf($this->specifications[self::SPECIFICATION_UPDATE], $table, $set);

        // process where
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $platform, $adapter->getDriver(), 'where');
            if (count($whereParts['parameters']) > 0) {
                $parameterContainer->merge($whereParts['parameters']);
            }
            $sql .= ' ' . sprintf($this->specifications[self::SPECIFICATION_WHERE], $whereParts['sql']);
        }
        $statement->setSql($sql);
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
        $table = $adapterPlatform->quoteIdentifier($this->table);

        $set = $this->set;
        if (is_array($set)) {
            $setSql = array();
            foreach ($set as $setName => $setValue) {
                $setSql[] = $adapterPlatform->quoteIdentifier($setName) . ' = ' . $adapterPlatform->quoteValue($setValue);
            }
            $set = implode(', ', $setSql);
        }

        $sql = sprintf($this->specifications[self::SPECIFICATION_UPDATE], $table, $set);
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $adapterPlatform, null, 'where');
            $sql .= ' ' . sprintf($this->specifications[self::SPECIFICATION_WHERE], $whereParts['sql']);
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
