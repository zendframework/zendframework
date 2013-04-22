<?php

namespace Zend\Db\Sql\Ddl\Column;

class Date extends Column
{
    protected $specification = '%1$s DATE %2$s%3$s';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();

        $types = array(self::TYPE_IDENTIFIER);
        $params[] = $this->name;


        $types[] = self::TYPE_LITERAL;
        $params[] = (!$this->isNullable) ? ' NOT NULL' : '';

        $types[] = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        $params[] = ($this->default !== null) ? $this->default : '';

        return array(array(
            $spec,
            $params,
            $types
        ));

    }
}