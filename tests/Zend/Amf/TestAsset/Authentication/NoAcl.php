<?php

namespace ZendTest\Amf\TestAsset\Authentication;

class NoAcl 
{
    function hello() 
    {
        return "hello!";
    }

    function initAcl() 
    {
        return false;
    }
}
