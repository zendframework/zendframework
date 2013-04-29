<?php

namespace Zend\Db\Sql\Ddl\Column;

class Decimal extends Column
{
    protected $specification = '%1$s DECIMAL(%2$s.%3$s)%4$s%5$s';
    protected $precision;
    protected $scale;

    public function __construct($name, $precision, $scale)
    {
        $this->name = $name;
        $this->precision = $precision;
        $this->scale = $scale;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL, self::TYPE_LITERAL);
        $params[] = $this->name;
        $params[] = $this->precision;
        $params[] = $this->scale;


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