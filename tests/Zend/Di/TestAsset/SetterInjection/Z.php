<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class Z
{
    public $y = null;
    public function setY(Y $y)
    {
        $this->y = $y;
    }
}