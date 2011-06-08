<?php

namespace ZendTest\Di\TestAsset\ConstructorInjection;

class Z
{
    public $y = null;
    public function __construct(Y $y)
    {
        $this->y = $y;
    }
}