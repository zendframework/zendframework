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
    Zend\Db\Adapter\ParameterContainerInterface,
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
class Delete extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    const SPECIFICATION_DELETE = 'delete';
    const SPECIFICATION_WHERE = 'where';

    protected $specifications = array(
        self::SPECIFICATION_DELETE => 'DELETE FROM %1$s',
        self::SPECIFICATION_WHERE => 'WHERE %1$s'
    );

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var null|string
     */
    protected $schema = null;

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
     * @param  null|string $table 
     * @param  null|string $schema
     * @return void
     */
    public function __construct($table = null, $schema = null)
    {
        if ($table) {
            $this->from($table, $schema);
        }
        $this->where = new Where();
    }

    /**
     * Create from statement
     * 
     * @param  string $table 
     * @param  null|string $schema
     * @return Delete
     */
    public function from($table, $schema = null)
    {
        $this->table = $table;
        if ($schema) {
            $this->schema = $schema;
        }
        return $this;
    }

    /**
     * Create where clause
     * 
     * @param  Where|Closure|string|array $predicate 
     * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
     * @return Delete
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

    /**
     * Prepare the delete statement
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

        if (!$parameterContainer instanceof ParameterContainerInterface) {
            $parameterContainer = new ParameterContainer();
            $statement->setParameterContainer($parameterContainer);
        }

        $table = $platform->quoteIdentifier($this->table);
        if ($this->schema != '') {
            $table = $platform->quoteIdentifier($this->schema)
                . $platform->getIdentifierSeparator()
                . $table;
        }

        $sql = sprintf($this->specifications[self::SPECIFICATION_DELETE], $table);

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
     * Get the SQL string, based on the platform
     *
     * Platform defaults to Sql92 if none provided
     * 
     * @param  null|PlatformInterface $platform 
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $table = $platform->quoteIdentifier($this->table);

        if ($this->schema != '') {
            $table = $platform->quoteIdentifier($this->schema) . $platform->getIdentifierSeparator() . $table;
        }

        $sql = sprintf($this->specifications[self::SPECIFICATION_DELETE], $table);

        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $platform, null, 'where');
            $sql .= ' ' . sprintf($this->specifications[self::SPECIFICATION_WHERE], $whereParts['sql']);
        }

        return $sql;
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
