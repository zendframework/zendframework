<?php

namespace ZendTest\Acl\TestAsset;

use Zend\Acl\Assertion,
    Zend\Acl;

class AssertionZF7973 implements Assertion {
    public function assert(Acl\Acl $acl, Acl\Role $role = null, Acl\Resource $resource = null, $privilege = null)
    {
        if($privilege != 'privilege') {
            return false;
        }

        return true;
    }
}
