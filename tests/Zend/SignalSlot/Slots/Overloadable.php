<?php
namespace ZendTest\SignalSlot\Slots;

class Overloadable
{
    public function __call($method, $args)
    {
        return $method;
    }
}
