<?php
namespace ZendTest\Messenger\Handlers;

class Invokable
{
    public function __invoke()
    {
        return __FUNCTION__;
    }
}
