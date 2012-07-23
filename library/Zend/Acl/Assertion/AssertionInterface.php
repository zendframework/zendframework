<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Acl
 */

namespace Zend\Acl\Assertion;

use Zend\Acl\Acl;
use Zend\Acl\Resource\ResourceInterface;
use Zend\Acl\Role\RoleInterface;

/**
 * @category   Zend
 * @package    Zend_Acl
 */
interface AssertionInterface
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl\Acl                        $acl
     * @param  Acl\Role\RoleInterface         $role
     * @param  Acl\Resource\ResourceInterface $resource
     * @param  string                         $privilege
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null);
}
