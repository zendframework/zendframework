<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class C
{
    public function __construct(D $d)
    {
        
    }
}
