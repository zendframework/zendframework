<?php

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Exception;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

class CreateTable extends AbstractSql implements SqlInterface
{
    const TABLE = 'table';
    const COLUMNS = 'columns';
    const CONSTRAINTS = 'constraints';
    const OPTIONS = 'options';

    protected $specifications = array(
        self::TABLE => 'CREATE TABLE %1$s',
        ' (',
        self::COLUMNS  => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        self::CONSTRAINTS => array(
            ",\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        "\n)"
    );

    protected $isTemporary = false;
    protected $table = '';
    protected $columns = array();
    protected $constraints = array();

    public function __construct($table = '', $isTemporary = false)
    {
        $this->table = $table;
    }

    public function setTemporary($temporary)
    {
        $this->isTemporary = (bool) $temporary;
    }

    public function setTable($name)
    {
        $this->table = $name;
    }

    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;
    }

    public function addConstraint(Constraint\ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;
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
            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
        }

        $sql = implode('', $sqls);
        return $sql;
    }

}