<?php

namespace ZendTest\Acl\TestAsset;

use Zend\Acl;

class MockAssertion implements Acl\Assertion\AssertionInterface
{
    protected $_returnValue;

    public function __construct($returnValue)
    {
        $this->_returnValue = (bool) $returnValue;
    }

    public function assert(Acl\Acl $acl, Acl\Role\RoleInterface $role = null, Acl\Resource\ResourceInterface $resource = null,
                           $privilege = null)
    {
       return $this->_returnValue;
    }
}
