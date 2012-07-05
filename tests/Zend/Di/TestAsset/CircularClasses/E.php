<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class E
{
    public function __construct(C $c)
    {
        
    }
}
