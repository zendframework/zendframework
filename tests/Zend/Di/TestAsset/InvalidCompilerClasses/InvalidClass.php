<?php

namespace ZendTest\Di\TestAsset\InvalidCompilerClasses;

class InvalidClass
{

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}
