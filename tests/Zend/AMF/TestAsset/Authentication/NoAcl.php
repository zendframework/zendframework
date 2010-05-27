<?php

namespace ZendTest\AMF\TestAsset\Authentication;

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
