<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class B
{
    public $a = null;
    public function __construct(A $a)
    {
        $this->a = $a;
    }
}