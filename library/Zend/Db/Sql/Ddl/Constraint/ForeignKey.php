<?php

namespace Zend\Db\Sql\Ddl\Constraint;

class ForeignKey extends AbstractConstraint
{
    protected $specification = 'CONSTRAINT %1$s FOREIGN KEY  (%2$s)';

    protected $referenceTable;
    protected $referenceColumn;

    protected $onDeleteRule;
    protected $onUpdateRule;

    public function getExpressionData()
    {
        $colCount = count($this->columns);
        $newSpecParts = array_fill(0, $colCount, '%s');
        $newSpecTypes = array_fill(0, $colCount, self::TYPE_IDENTIFIER);

        $newSpec = sprintf($this->specification, implode(', ', $newSpecParts));

        return array(
            $newSpec,
            $this->columns,
            $newSpecTypes
        );
    }

}