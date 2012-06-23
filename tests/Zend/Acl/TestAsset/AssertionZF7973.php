<?php

namespace ZendTest\Acl\TestAsset;

use Zend\Acl\Assertion\AssertionInterface,
    Zend\Acl;

class AssertionZF7973 implements AssertionInterface {
    public function assert(Acl\Acl $acl, Acl\Role\RoleInterface $role = null, Acl\Resource\ResourceInterface $resource = null, $privilege = null)
    {
        if($privilege != 'privilege') {
            return false;
        }

        return true;
    }
}
