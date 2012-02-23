<?php

namespace Zend\Db\Sql\Predicate;

class Literal implements Predicate
{
    protected $literal = '';
    
    public function __construct($literal = null)
    {
        if ($literal) {
            $this->setLiteral($literal);
        }
    }
    
    public function setLiteral($literal)
    {
        $this->literal = $literal;
    }
    
    public function toPreparedString($type = null)
    {
        return $this->literal;
    }
    
    public function toString()
    {
        return $this->literal;
    }
}
