<?php

namespace ZendTest\Di\TestAsset\ConfigParameter;

class A
{
    public $someInt = null;
    public $m = null;
    
    public function setSomeInt($value)
    {
        $this->someInt = $value;
    }
    
    public function injectM($m)
    {
        $this->m = $m;
    }
    
}