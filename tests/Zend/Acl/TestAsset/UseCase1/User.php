<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Role;

class User implements Role\RoleInterface
{
    public $role = 'guest';
    public function getRoleId()
    {
        return $this->role;
    }
}
