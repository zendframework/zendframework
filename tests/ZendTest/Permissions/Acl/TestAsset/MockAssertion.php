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

class MockAssertion implements Acl\Assertion\AssertionInterface
{
    protected $_returnValue;

    public function __construct($returnValue)
    {
        $this->_returnValue = (bool) $returnValue;
    }

    public function assert(Acl\Acl $acl, Acl\Role\RoleInterface $role = null, Acl\Resource\ResourceInterface $resource = null,
                           $privilege = null)
    {
       return $this->_returnValue;
    }
}
