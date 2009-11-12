<?php
require_once 'Zend/Acl/Assert/Interface.php';

class Zend_Acl_AclTest_AssertionZF7973 implements Zend_Acl_Assert_Interface {
    public function assert(Zend_Acl $acl,
                Zend_Acl_Role_Interface $role = null,
                Zend_Acl_Resource_Interface $resource = null,
                $privilege = null)
    {
        if($privilege != 'privilege') {
            return false;
        }

        return true;
    }
}
