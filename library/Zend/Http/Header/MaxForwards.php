<?php

namespace Zend\Http\Header;

class MaxForwards implements HeaderDescription
{
    
    protected $value = null;
    
    public static function fromString($headerLine)
    {
        // @todo
    }
    
    public function getName()
    {
        return 'MaxForwards';
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function toString()
    {
        
    }
    
}
