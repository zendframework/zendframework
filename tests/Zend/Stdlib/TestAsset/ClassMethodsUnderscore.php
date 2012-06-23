<?php

namespace ZendTest\Stdlib\TestAsset;

class ClassMethodsUnderscore
{
    protected $foo_bar = '1';
    
    protected $foo_bar_baz = '2';
    
    public function getFooBar()
    {
        return $this->foo_bar;
    }
    
    public function setFooBar($value)
    {
        $this->foo_bar = $value;
        return $this;
    }
    
    public function getFooBarBaz()
    {
        return $this->foo_bar_baz;
    }
    
    public function setFooBarBaz($value)
    {
        $this->foo_bar_baz = $value;
        return $this;
    }
}