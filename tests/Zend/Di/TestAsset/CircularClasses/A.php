<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class A
{
    public function __construct(B $b)
    {
        
    }
}
