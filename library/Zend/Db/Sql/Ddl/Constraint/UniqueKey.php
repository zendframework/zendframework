<?php

namespace Zend\Db\Sql\Ddl\Constraint;

class UniqueKey extends AbstractConstraint
{
    protected $specification = 'CONSTRAINT UNIQUE KEY %1$s(...)';

    public function __construct($column, $name = null)
    {
        $this->setColumns($column);
        $this->name = $name;
    }

    public function getExpressionData()
    {
        $colCount = count($this->columns);

        $values = array();
        $values[] = ($this->name) ? $this->name . ' ' : '';

        $newSpecTypes = array(self::TYPE_IDENTIFIER);

        $newSpecParts = array();


        for ($i = 0; $i < $colCount; $i++) {
            $newSpecParts[] = '%' . ($i+2) . '$s';
            $newSpecTypes[] = self::TYPE_IDENTIFIER;
        }

        $newSpec = str_replace('...', implode(', ', $newSpecParts), $this->specification);
//var_dump($newSpec, $newSpecTypes);
        return array(array(
            $newSpec,
            array_merge($values, $this->columns),
            $newSpecTypes
        ));
    }

}