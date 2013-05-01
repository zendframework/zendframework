<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @package Zend_Db
 */

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

class CreateTable extends AbstractSql implements SqlInterface
{
    const TABLE = 'table';
    const COLUMNS = 'columns';
    const CONSTRAINTS = 'constraints';

    /**
     * Specifications for Sql String generation
     * @var array
     */
    protected $specifications = array(
        self::TABLE => 'CREATE TABLE %1$s (',
        self::COLUMNS  => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        self::CONSTRAINTS => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        )
    );

    /**
     * @var bool
     */
    protected $isTemporary = false;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $constraints = array();

    /**
     * @param string $table
     * @param bool   $isTemporary
     */
    public function __construct($table = '', $isTemporary = false)
    {
        $this->table = $table;
        $this->setTemporary($isTemporary);
    }

    /**
     * @param $temporary
     * @return $this
     */
    public function setTemporary($temporary)
    {
        $this->isTemporary = (bool) $temporary;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTemporary()
    {
        return $this->isTemporary;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setTable($name)
    {
        $this->table = $name;

        return $this;
    }

    /**
     * @param Column\ColumnInterface $column
     * @return $this
     */
    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param Constraint\ConstraintInterface $constraint
     * @return $this
     */
    public function addConstraint(Constraint\ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @param  string|null $key
     * @return array
     */
    public function getRawState($key = null)
    {
        $rawState = array(
            self::TABLE      => $this->table,
            self::COLUMNS    => $this->columns,
            self::CONSTRAINTS => $this->constraints,
        );

        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * @param  PlatformInterface $adapterPlatform
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            if (is_int($name)) {
                $sqls[] = $specification;
                continue;
            }
            $parameters[$name] = $this->{'process' . $name}($adapterPlatform, null, null, $sqls, $parameters);
            if ($specification && is_array($parameters[$name]) && ($parameters[$name] != array(array()))) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
            if (stripos($name, 'table') === false && $parameters[$name] !== array(array())) {
                $sqls[] = ",\n";
            }
        }

        // remove last ,
        if (count($sqls) > 2) {
            array_pop($sqls);
        }

        $sql = implode('', $sqls) . "\n)";

        return $sql;
    }

    protected function processTable(PlatformInterface $adapterPlatform = null)
    {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY';
        }
        $ret[] = $adapterPlatform->quoteIdentifier($this->table);

        return $ret;
    }

    protected function processColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->columns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform)->getSql();
        }

        return array($sqls);
    }

    protected function processConstraints(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapterPlatform)->getSql();
        }

        return array($sqls);
    }

}
