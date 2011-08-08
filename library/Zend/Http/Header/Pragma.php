<?php

namespace Zend\Http\Header;

class Pragma implements HeaderDescription
{
    
    protected $value = null;
    
    public static function fromString($headerLine)
    {
        // @todo
    }
    
    public function getName()
    {
        return 'Pragma';
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function toString()
    {
        
    }
    
}
