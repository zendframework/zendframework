<?php

namespace ZendTest\Stdlib\TestAsset;

class ClassMethodsCamelCaseMissing
{
    protected $fooBar = '1';
    
    protected $fooBarBaz = '2';
    
    public function getFooBar()
    {
        return $this->fooBar;
    }
    
    public function setFooBar($value)
    {
        $this->fooBar = $value;
        return $this;
    }
    
    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }
    
    /*
     * comment to detection verification
     * 
    public function setFooBarBaz($value)
    {
        $this->fooBarBaz = $value;
        return $this;
    }
    */
}