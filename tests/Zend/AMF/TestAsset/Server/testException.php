<?php

namespace ZendTest\AMF\TestAsset\Server;

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
