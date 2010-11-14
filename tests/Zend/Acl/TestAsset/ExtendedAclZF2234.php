<?php

namespace ZendTest\Acl\TestAsset;
use Zend\Acl;

class ExtendedAclZF2234 extends Acl\Acl
{
    public function roleDFSVisitAllPrivileges(Acl\Role $role, Acl\Resource $resource = null,
                                              &$dfs = null)
    {
        return $this->_roleDFSVisitAllPrivileges($role, $resource, $dfs);
    }

    public function roleDFSOnePrivilege(Acl\Role $role, Acl\Resource $resource = null,
                                        $privilege = null)
    {
        return $this->_roleDFSOnePrivilege($role, $resource, $privilege);
    }

    public function roleDFSVisitOnePrivilege(Acl\Role $role, Acl\Resource $resource = null,
                                             $privilege = null, &$dfs = null)
    {
        return $this->_roleDFSVisitOnePrivilege($role, $resource, $privilege, $dfs);
    }
}
