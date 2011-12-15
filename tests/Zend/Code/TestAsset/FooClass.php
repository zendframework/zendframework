<?php

namespace ZendTest\Code\TestAsset;

include __DIR__ . '/foo/bar/baz.php';

use A\B\C,
    A\B\C\D as E;
use Foo\Bar\Baz as FooBarBaz;

abstract class FooClass implements \ArrayAccess, E\Blarg, Local\SubClass
{
    const BAR = 5;
    const FOO = self::BAR;
    
    protected static $bar = 'value';
    
    final public function fooBarBaz()
    {
        // foo
    }

}
