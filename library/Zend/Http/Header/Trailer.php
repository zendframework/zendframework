<?php

namespace Zend\Http\Header;

class Trailer implements HeaderDescription
{
    
    protected $value = null;
    
    public static function fromString($headerLine)
    {
        // @todo
    }
    
    public function getName()
    {
        return 'Trailer';
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function toString()
    {
        
    }
    
}
