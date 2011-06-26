<?php

namespace ZendTest\Di\TestAsset\CircularClasses;

class B
{
    public function __construct(A $a) {}
}