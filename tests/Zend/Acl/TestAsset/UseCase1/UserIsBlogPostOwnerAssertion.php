<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Assertion,
    Zend\Acl as ZendAcl;

class UserIsBlogPostOwnerAssertion implements Assertion
{

    public $lastAssertRole = null;
    public $lastAssertResource = null;
    public $lastAssertPrivilege = null;
    public $assertReturnValue = true;

    public function assert(ZendAcl\Acl $acl, ZendAcl\Role $user = null, ZendAcl\Resource $blogPost = null, $privilege = null)
    {
        $this->lastAssertRole      = $user;
        $this->lastAssertResource  = $blogPost;
        $this->lastAssertPrivilege = $privilege;
        return $this->assertReturnValue;
    }
}
