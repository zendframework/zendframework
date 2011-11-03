<?php

namespace ZendTest\Di\TestAsset\InjectionClasses;

class B
{
    public $id = null;
    public function __construct($id = null)
    {
        $this->id = $id;
    }
}
