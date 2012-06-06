<?php

namespace ZendTest\Di\TestAsset\InheritanceClasses;

class A
{
    public $test;
 
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }
}
