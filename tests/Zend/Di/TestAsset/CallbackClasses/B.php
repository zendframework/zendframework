<?php

namespace ZendTest\Di\TestAsset\CallbackClasses;

class B
{
    public $c, $params = null;

    public static function factory(C $c, array $params = array())
    {
        $b = new B();
        $b->c = $c;
        $b->params = $params;
        return $b;
    }

    protected function __construct()
    {
        // no dice
    }
}
