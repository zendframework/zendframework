<?php

namespace Zend\Db\Sql\Ddl\Column;

class Boolean extends Column
{
    protected $specification = '%1$s TINYINT NOT NULL';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array($this->name);
        $types = array(self::TYPE_IDENTIFIER);

        return array(array(
            $spec,
            $params,
            $types
        ));

    }
}