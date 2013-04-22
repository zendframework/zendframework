<?php

namespace Zend\Db\Sql\Ddl\Column;

class Integer extends Column
{
    protected $length;

    public function __construct($name, $length)
    {
        $this->name = $name;
        $this->length = $length;
    }
}