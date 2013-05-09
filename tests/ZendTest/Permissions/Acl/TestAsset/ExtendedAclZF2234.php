<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Permissions
 */

namespace ZendTest\Permissions\Acl\TestAsset;

use Zend\Permissions\Acl;

class ExtendedAclZF2234 extends Acl\Acl
{
    public function exroleDFSVisitAllPrivileges(Acl\Role\RoleInterface $role, Acl\Resource\ResourceInterface $resource = null,
                                              &$dfs = null)
    {
        return $this->roleDFSVisitAllPrivileges($role, $resource, $dfs);
    }

    public function exroleDFSOnePrivilege(Acl\Role\RoleInterface $role, Acl\Resource\ResourceInterface $resource = null,
                                        $privilege = null)
    {
        return $this->roleDFSOnePrivilege($role, $resource, $privilege);
    }

    public function exroleDFSVisitOnePrivilege(Acl\Role\RoleInterface $role, Acl\Resource\ResourceInterface $resource = null,
                                             $privilege = null, &$dfs = null)
    {
        return $this->roleDFSVisitOnePrivilege($role, $resource, $privilege, $dfs);
    }
}
