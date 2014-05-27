<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Driver\DriverInterface;

/**
 *
 * @property Where $where
 */
class Delete extends AbstractPreparableSql
{
    /**@#+
     * @const
     */
    const SPECIFICATION_DELETE = 'delete';
    const SPECIFICATION_WHERE = 'where';
    /**@#-*/

    /**
     * @var array Specifications
     */
    protected $specifications = array(
        self::SPECIFICATION_DELETE => 'DELETE FROM %1$s',
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
     * @var array
     */
    protected $set = array();

    /**
     * @var null|string|Where
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
            $this->from($table);
        }
        $this->where = new Where();
    }

    /**
     * Create from statement
     *
     * @param  string|TableIdentifier $table
     * @return Delete
     */
    public function from($table)
    {
        $this->table = $table;
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
     * Create where clause
     *
     * @param  Where|\Closure|string|array $predicate
     * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
     * @return Delete
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

    protected function processDelete(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        return sprintf(
            $this->specifications[static::SPECIFICATION_DELETE],
            $this->resolveTable($this->table, $platform, $driver, $parameterContainer)
        );
    }

    protected function processWhere(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->where->count() == 0) {
            return null;
        }
        $whereParts = $this->processExpression($this->where, $platform, $driver, $parameterContainer, 'where');
        if ($parameterContainer) {
            $parameterContainer->merge($whereParts->getParameterContainer());
        }
        return sprintf(
            $this->specifications[static::SPECIFICATION_WHERE],
            $whereParts->getSql()
        );
    }

    /**
     * Property overloading
     *
     * Overloads "where" only.
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
