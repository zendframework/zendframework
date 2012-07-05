<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class X
{
    public $one = null;
    public $two = null;
    public function setOne($one)
    {
        $this->one = $one;
    }
    public function setTwo($two)
    {
        $this->two = $two;
    }
}
