<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class B
{
    public $a = null;
    public function setA(A $a)
    {
        $this->a = $a;
    }
}