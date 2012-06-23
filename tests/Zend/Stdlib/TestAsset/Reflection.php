<?php

namespace ZendTest\Stdlib\TestAsset;

class Reflection
{
    public $foo = '1';

    protected $fooBar = '2';

    private $fooBarBaz = '3';

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }
}