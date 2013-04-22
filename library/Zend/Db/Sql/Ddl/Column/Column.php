<?php

namespace Zend\Db\Sql\Ddl\Column;

class Column implements ColumnInterface
{
    protected $specification = '%s %s';

    protected $name = null;
    protected $type = 'INTEGER';
    protected $isNullable = false;
    protected $default = null;
    protected $options = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isNullable()
    {
        return $this->isNullable;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();
        $params[] = $this->name;
        $params[] = $this->type;

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);

        if (!$this->isNullable) {
            $params[1] .= ' NOT NULL';
        }

        if ($this->default !== null) {
            $spec .= ' DEFAULT %s';
            $params[] = $this->default;
            $types[] = self::TYPE_VALUE;
        }

        return array(array(
            $spec,
            $params,
            $types
        ));

    }

}
