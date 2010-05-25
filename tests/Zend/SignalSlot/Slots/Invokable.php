<?php
namespace ZendTest\SignalSlot\Slots;

class Invokable
{
    public function __invoke()
    {
        return __FUNCTION__;
    }
}
