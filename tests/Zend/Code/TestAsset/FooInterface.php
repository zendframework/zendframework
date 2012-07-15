<?php

namespace ZendTest\Code\TestAsset;

interface FooInterface extends \ArrayAccess
{
    const BAR = 5;
    const FOO = self::BAR;

    public function fooBarBaz();

}
