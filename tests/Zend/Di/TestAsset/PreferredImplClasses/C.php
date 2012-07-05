<?php

namespace ZendTest\Di\TestAsset\PreferredImplClasses;

class C
{
    public $a = null;
    public function setA(A $a)
    {
        $this->a = $a;
    }
}
