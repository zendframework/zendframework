<?php

namespace Zend\Db\Sql\Ddl\Column;

class Integer extends Column
{
    protected $length;

    public function __construct($name, $length, $nullable = false, $default = null, array $options = array())
    {
        $this->setName($name);
        $this->setLength($length);
        $this->setNullable($nullable);
        $this->setDefault($default);
        $this->setOptions($options);
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getLength()
    {
        return $this->length;
    }
}