<?php

namespace Zend\Http\Header;

class AcceptCharset implements HeaderDescription
{
    
    protected $value = null;
    
    public static function fromString($headerLine)
    {
        // @todo
    }
    
    public function getName()
    {
        return 'Accept-Charset';
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function toString()
    {
        
    }
    
}
