<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class D
{
    public $a = null;
    public function setA($a)
    {
        $this->a = $a;
    }
}
