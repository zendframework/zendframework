<?php

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Acl\Acl as ZendAcl;

class Acl 
{
    function hello() 
    {
        return "hello!";
    }

    function hello2() 
    {
        return "hello2!";
    }

    function initAcl(ZendAcl $acl) 
    {
        $acl->allow("testrole", null, "hello");
        $acl->allow("testrole2", null, "hello2");
        return true;
    }
}
