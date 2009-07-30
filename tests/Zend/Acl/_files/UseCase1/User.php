<?php

class Zend_Acl_UseCase1_User implements Zend_Acl_Role_Interface
{
    public $role = 'guest';
    public function getRoleId()
    {
        return $this->role;
    }
}
