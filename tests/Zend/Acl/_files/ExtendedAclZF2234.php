<?php

class Zend_Acl_ExtendedAclZF2234 extends Zend_Acl
{
    public function roleDFSVisitAllPrivileges(Zend_Acl_Role_Interface $role, Zend_Acl_Resource_Interface $resource = null,
                                              &$dfs = null)
    {
        return $this->_roleDFSVisitAllPrivileges($role, $resource, $dfs);
    }

    public function roleDFSOnePrivilege(Zend_Acl_Role_Interface $role, Zend_Acl_Resource_Interface $resource = null,
                                        $privilege = null)
    {
        return $this->_roleDFSOnePrivilege($role, $resource, $privilege);
    }

    public function roleDFSVisitOnePrivilege(Zend_Acl_Role_Interface $role, Zend_Acl_Resource_Interface $resource = null,
                                             $privilege = null, &$dfs = null)
    {
        return $this->_roleDFSVisitOnePrivilege($role, $resource, $privilege, $dfs);
    }
}