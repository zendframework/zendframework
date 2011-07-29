<?php

namespace ZendTest\Di\TestAsset\ConstructorInjection;

class X
{
    public $one = null;
    public $two = null;
    public function __construct($one, $two)
    {
        $this->one = $one;
        $this->two = $two;
    }
}