<?php
/**
 *
 * User: Timo Tewes
 * Date: 19.01.13
 * Time: 10:08
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

    public function testHasACLChecksDefaultACL()
    {
        $aclContainer = $this->_getAcl();
        /** @var $acl \Zend\Permissions\Acl\Acl */
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasACL());
        $this->_helper->setDefaultAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasACLChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        /** @var $acl \Zend\Permissions\Acl\Acl */
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasAcl());
        $this->_helper->setAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasRoleChecksDefaultRole()
    {
        $aclContainer = $this->_getAcl();
        /** @var $role \Zend\Permissions\Acl\Role */
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setDefaultRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }

    public function testHasRoleChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        /** @var $role \Zend\Permissions\Acl\Role */
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->_helper->setDefaultAcl(null);
        $this->_helper->setAcl(null);
        $this->_helper->setDefaultRole(null);
        $this->_helper->setRole(null);
    }


}
