<?php

namespace ZendTest\Amf\TestAsset\Server;

class testException
{
    public function __construct() 
    {
        throw new \Exception("Oops, exception!");
    }

    public function hello() 
    {
        return "hello";
    }
}
