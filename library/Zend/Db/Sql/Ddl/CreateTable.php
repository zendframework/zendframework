<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;

class CreateTable extends AbstractSql implements SqlInterface
{
    const COLUMNS     = 'columns';
    const CONSTRAINTS = 'constraints';
    const TABLE       = 'table';

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $constraints = array();

    /**
     * @var bool
     */
    protected $isTemporary = false;

    /**
     * Specifications for Sql String generation
     * @var array
     */
    protected $specifications = array(
        self::TABLE => 'CREATE %1$sTABLE %2$s (',
        self::COLUMNS  => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        'combinedBy' => ",",
        self::CONSTRAINTS => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        'statementEnd' => '%1$s',
    );

    /**
     * @var string
     */
    protected $table = '';

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
     * @param  bool $temporary
     * @return self
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
     * @param  string $name
     * @return self
     */
    public function setTable($name)
    {
        $this->table = $name;
        return $this;
    }

    /**
     * @param  Column\ColumnInterface $column
     * @return self
     */
    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param  Constraint\ConstraintInterface $constraint
     * @return self
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
            self::COLUMNS     => $this->columns,
            self::CONSTRAINTS => $this->constraints,
            self::TABLE       => $this->table,
        );

        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    protected function processTable(PlatformInterface $adapterPlatform = null)
    {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY ';
        } else {
            $ret[] = '';
        }

        $ret[] = $adapterPlatform->quoteIdentifier($this->table);
        return $ret;
    }

    protected function processColumns(PlatformInterface $adapterPlatform = null)
    {
        if (!$this->columns) {
            return null;
        }

        $sqls = array();
        foreach ($this->columns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform)->getSql();
        }
        return array($sqls);
    }

    protected function processCombinedby(PlatformInterface $adapterPlatform = null)
    {
        if ($this->constraints && $this->columns) {
            return $this->specifications['combinedBy'];
        }
    }

    protected function processConstraints(PlatformInterface $adapterPlatform = null)
    {
        if (!$this->constraints) {
            return null;
        }

        $sqls = array();
        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapterPlatform)->getSql();
        }
        return array($sqls);
    }

    protected function processStatementEnd(PlatformInterface $adapterPlatform = null)
    {
        return array("\n)");
    }
}
