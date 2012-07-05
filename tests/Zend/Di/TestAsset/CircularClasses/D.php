<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class D
{
    public function __construct(E $e)
    {
        
    }
}
