<?php

namespace Zend\Db\Sql\Ddl\Constraint;

class Check extends AbstractConstraint
{
    protected $specification = 'PRIMARY KEY (%s)';

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