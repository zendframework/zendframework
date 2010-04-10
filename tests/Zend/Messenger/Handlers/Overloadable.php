<?php
namespace ZendTest\Messenger\Handlers;

class Overloadable
{
    public function __call($method, $args)
    {
        return $method;
    }
}
