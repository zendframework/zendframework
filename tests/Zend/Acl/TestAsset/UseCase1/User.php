<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Role;

class User implements Role
{
    public $role = 'guest';
    public function getRoleId()
    {
        return $this->role;
    }
}
