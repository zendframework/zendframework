<?php

namespace ZendTest\Acl\TestAsset;
use Zend\Acl;

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
