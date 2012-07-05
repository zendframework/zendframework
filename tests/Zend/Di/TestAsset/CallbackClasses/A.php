<?php


namespace ZendTest\Di\TestAsset\CallbackClasses;

class A
{
    public static function factory()
    {
        return new self();
    }

    private function __construct() {}
}
