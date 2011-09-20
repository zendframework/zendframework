<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class Y
{
    public $x = null;
    public function setX(X $x)
    {
        $this->x = $x;
    }
}
