<?php

namespace ZendTest\Stdlib\TestAsset;

class ClassMethodsCamelCase
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
    
    public function setFooBarBaz($value)
    {
        $this->fooBarBaz = $value;
        return $this;
    }
}