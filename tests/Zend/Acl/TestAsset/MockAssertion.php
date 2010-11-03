<?php

namespace ZendTest\Acl\TestAsset;

use Zend\Acl;

class MockAssertion implements Acl\Assertion
{
    protected $_returnValue;

    public function __construct($returnValue)
    {
        $this->_returnValue = (bool) $returnValue;
    }

    public function assert(Acl\Acl $acl, Acl\Role $role = null, Acl\Resource $resource = null,
                           $privilege = null)
    {
       return $this->_returnValue;
    }
}
