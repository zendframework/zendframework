<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Assertion\AssertionInterface,
    Zend\Acl as ZendAcl;

class UserIsBlogPostOwnerAssertion implements AssertionInterface
{

    public $lastAssertRole = null;
    public $lastAssertResource = null;
    public $lastAssertPrivilege = null;
    public $assertReturnValue = true;

    public function assert(ZendAcl\Acl $acl, ZendAcl\Role\RoleInterface $user = null, ZendAcl\Resource\ResourceInterface $blogPost = null, $privilege = null)
    {
        $this->lastAssertRole      = $user;
        $this->lastAssertResource  = $blogPost;
        $this->lastAssertPrivilege = $privilege;
        return $this->assertReturnValue;
    }
}
