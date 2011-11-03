<?php

namespace ZendTest\Di\TestAsset\InjectionClasses;

class A
{
    public $bs = array();

    public function addB(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectBOnce(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectBTwice(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectSplitDependency(B $b, $somestring)
    {
        $b->id = $somestring;
        $this->bs[] = $b;
    }
}
