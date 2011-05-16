<?php
namespace ZendTest\Di\TestAsset;

class InjectedMethod
{
    public function setObject($o)
    {
        $this->object = $o;
    }
}
