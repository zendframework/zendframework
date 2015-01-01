<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Permissions\Acl\TestAsset\UseCase1;

use Zend\Permissions\Acl\Role;

class User implements Role\RoleInterface
{
    public $role = 'guest';
    public function getRoleId()
    {
        return $this->role;
    }
}
