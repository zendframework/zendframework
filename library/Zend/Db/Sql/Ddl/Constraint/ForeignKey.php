<?php

namespace Zend\Db\Sql\Ddl\Constraint;

class ForeignKey extends AbstractConstraint
{
    protected $specification = 'CONSTRAINT %1$s FOREIGN KEY (%2$s) REFERENCES %3$s (%4$s) ON DELETE %5$s ON UPDATE %6$s';

    protected $name;

    protected $referenceTable;
    protected $referenceColumn;

    protected $onDeleteRule = 'NO ACTION';
    protected $onUpdateRule = 'NO ACTION';

    public function __construct($name, $column, $referenceTable, $referenceColumn, $onDeleteRule = null, $onUpdateRule = null)
    {
        $this->setName($name);
        $this->setColumns($column);
        $this->setReferenceTable($referenceTable);
        $this->setReferenceColumn($referenceColumn);
        (!$onDeleteRule) ?: $this->setOnDeleteRule($onDeleteRule);
        (!$onUpdateRule) ?: $this->setOnUpdateRule($onUpdateRule);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setReferenceTable($referenceTable)
    {
        $this->referenceTable = $referenceTable;
    }

    public function getReferenceTable()
    {
        return $this->referenceTable;
    }

    public function setReferenceColumn($referenceColumn)
    {
        $this->referenceColumn = $referenceColumn;
    }

    public function getReferenceColumn()
    {
        return $this->referenceColumn;
    }

    public function setOnDeleteRule($onDeleteRule)
    {
        $this->onDeleteRule = $onDeleteRule;
    }

    public function getOnDeleteRule()
    {
        return $this->onDeleteRule;
    }

    public function setOnUpdateRule($onUpdateRule)
    {
        $this->onUpdateRule = $onUpdateRule;
    }

    public function getOnUpdateRule()
    {
        return $this->onUpdateRule;
    }

    public function getExpressionData()
    {

        return array(array(
            $this->specification,
            array(
                $this->name,
                $this->columns[0],
                $this->referenceTable,
                $this->referenceColumn,
                $this->onDeleteRule,
                $this->onUpdateRule
            ),
            array(
                self::TYPE_IDENTIFIER,
                self::TYPE_IDENTIFIER,
                self::TYPE_IDENTIFIER,
                self::TYPE_IDENTIFIER,
                self::TYPE_LITERAL,
                self::TYPE_LITERAL,
            )

        ));
    }

}