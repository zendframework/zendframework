<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class X
{
    public function __construct(X $x)
    {
        
    }
}
