<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Acl
 */

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Assertion\AssertionInterface;
use Zend\Acl as ZendAcl;

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
