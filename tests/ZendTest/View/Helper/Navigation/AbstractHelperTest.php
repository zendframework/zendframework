<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper\Navigation;


class AbstractHelperTest extends AbstractTest
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend\View\Helper\Navigation';

    /**
     * View helper
     *
     * @var \Zend\View\Helper\Navigation\Breadcrumbs
     */
    protected $_helper;

    protected function tearDown()
    {
        parent::tearDown();
        $this->_helper->setDefaultAcl(null);
        $this->_helper->setAcl(null);
        $this->_helper->setDefaultRole(null);
        $this->_helper->setRole(null);
    }

    public function testHasACLChecksDefaultACL()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasACL());
        $this->_helper->setDefaultAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasACLChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasAcl());
        $this->_helper->setAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasRoleChecksDefaultRole()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setDefaultRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }

    public function testHasRoleChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }
}
