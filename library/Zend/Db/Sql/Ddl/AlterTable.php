<?php

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Exception;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

class AlterTable extends AbstractSql implements SqlInterface
{
    const TABLE = 'table';
    const ADD_COLUMNS = 'addColumns';
    const CHANGE_COLUMNS = 'changeColumns';
    const DROP_COLUMNS = 'dropColumns';
    const ADD_CONSTRAINTS = 'addConstraints';
    const DROP_CONSTRAINTS = 'dropConstraints';

    protected $specifications = array(
        self::TABLE => "ALTER TABLE %1\$s \n",
        self::ADD_COLUMNS  => array(
            "%1\$s" => array(
                array(1 => 'ADD COLUMN %1$s', 'combinedby' => ",\n")
            )
        ),
        self::CHANGE_COLUMNS  => array(
            "%1\$s" => array(
                array(2 => 'CHANGE COLUMN %1$s %2$s', 'combinedby' => ",\n"),
            )
        ),
        self::DROP_COLUMNS  => array(
            "%1\$s" => array(
                array(1 => 'DROP COLUMN %1$s', 'combinedby' => ",\n"),
            )
        ),
        self::ADD_CONSTRAINTS  => array(
            "%1\$s" => array(
                array(1 => 'ADD %1$s', 'combinedby' => ",\n"),
            )
        ),
        self::DROP_CONSTRAINTS  => array(
            "%1\$s" => array(
                array(1 => 'DROP CONSTRAINT %1$s', 'combinedby' => ",\n"),
            )
        )
    );

    protected $table = '';
    protected $addColumns = array();
    protected $changeColumns = array();
    protected $dropColumns = array();
    protected $addConstraints = array();
    protected $dropConstraints = array();

    public function __construct($table = '')
    {
        ($table) ? $this->setTable($table) : null;
    }

    public function setTable($name)
    {
        $this->table = $name;
    }

    public function addColumn(Column\ColumnInterface $column)
    {
        $this->addColumns[] = $column;
    }

    public function changeColumn($name, Column\ColumnInterface $column)
    {
        $this->changeColumns[$name] = $column;
    }

    public function dropColumn($name)
    {
        $this->dropColumns[] = $name;
    }

    public function dropConstraint($name)
    {
        $this->dropConstraints[] = $name;
    }

    public function addConstraint(Constraint\ConstraintInterface $constraint)
    {
        $this->addConstraints[] = $constraint;
    }


    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($adapterPlatform, null, null, $sqls, $parameters);
            if ($specification && is_array($parameters[$name]) && ($parameters[$name] != array(array()))) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
            if (stripos($name, 'table') === false && $parameters[$name] !== array(array())) {
                $sqls[] = ",\n";
            }
        }

        // remove last ,\n
        array_pop($sqls);

        $sql = implode('', $sqls);
        return $sql;
    }

    protected function processTable(PlatformInterface $adapterPlatform = null)
    {
        return array($adapterPlatform->quoteIdentifier($this->table));
    }

    protected function processAddColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->addColumns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform)->getSql();
        }
        return array($sqls);
    }

    protected function processChangeColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->changeColumns as $name => $column) {
            $sqls[] = array(
                $adapterPlatform->quoteIdentifier($name),
                $this->processExpression($column, $adapterPlatform)->getSql()
            );
        }
        return array($sqls);
    }


    protected function processDropColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->dropColumns as $column) {
             $sqls[] = $adapterPlatform->quoteIdentifier($column);
        }

        return array($sqls);
    }


    protected function processAddConstraints(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->addConstraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapterPlatform);
        }

        return array($sqls);
    }


    protected function processDropConstraints(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->dropConstraints as $constraint) {
            $sqls[] = $adapterPlatform->quoteIdentifier($constraint);
        }

        return array($sqls);
    }

}
