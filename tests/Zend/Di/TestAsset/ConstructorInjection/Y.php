<?php

namespace ZendTest\Di\TestAsset\ConstructorInjection;

class Y
{
    public $x = null;
    public function __construct(X $x)
    {
        $this->x = $x;
    }
}
