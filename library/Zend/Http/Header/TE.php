<?php

namespace Zend\Http\Header;

class TE implements HeaderDescription
{
    
    protected $value = null;
    
    public static function fromString($headerLine)
    {
        // @todo
    }
    
    public function getName()
    {
        return 'TE';
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function toString()
    {
        
    }
    
}
