<?php

namespace ZendTest\Di\TestAsset\ConstructorInjection;

class B
{
    public $a = null;
    public function __construct(A $a)
    {
        $this->a = $a;
    }
}