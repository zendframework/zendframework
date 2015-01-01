<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Permissions\Rbac;

use Zend\Permissions\Rbac;

/**
 * @group      Zend_Rbac
 */
class RbacTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Permissions\Rbac\Rbac
     */
    protected $rbac;

    public function setUp()
    {
        $this->rbac = new Rbac\Rbac();
    }

    public function testIsGrantedAssertion()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $true  = new TestAsset\SimpleTrueAssertion();
        $false = new TestAsset\SimpleFalseAssertion();

        $roleNoMatch = new TestAsset\RoleMustMatchAssertion($bar);
        $roleMatch   = new TestAsset\RoleMustMatchAssertion($foo);

        $foo->addPermission('can.foo');
        $bar->addPermission('can.bar');

        $this->rbac->addRole($foo);
        $this->rbac->addRole($bar);

        $this->assertEquals(true, $this->rbac->isGranted($foo, 'can.foo', $true));
        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.bar', $false));

        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.bar', $roleNoMatch));
        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.foo', $roleNoMatch));

        $this->assertEquals(true, $this->rbac->isGranted($foo, 'can.foo', $roleMatch));
    }

    public function testIsGrantedSingleRole()
    {
        $foo = new Rbac\Role('foo');
        $foo->addPermission('can.bar');

        $this->rbac->addRole($foo);

        $this->assertEquals(true, $this->rbac->isGranted('foo', 'can.bar'));
        $this->assertEquals(false, $this->rbac->isGranted('foo', 'can.baz'));
    }

    public function testIsGrantedChildRoles()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $foo->addPermission('can.foo');
        $bar->addPermission('can.bar');

        $this->rbac->addRole($foo);
        $this->rbac->addRole($bar, $foo);

        $this->assertEquals(true, $this->rbac->isGranted('foo', 'can.bar'));
        $this->assertEquals(true, $this->rbac->isGranted('foo', 'can.foo'));
        $this->assertEquals(true, $this->rbac->isGranted('bar', 'can.bar'));

        $this->assertEquals(false, $this->rbac->isGranted('foo', 'can.baz'));
        $this->assertEquals(false, $this->rbac->isGranted('bar', 'can.baz'));
    }

    /**
     * @covers Zend\Permissions\Rbac\Rbac::hasRole()
     */
    public function testHasRole()
    {
        $foo = new Rbac\Role('foo');
        $snafu = new TestAsset\RoleTest('snafu');

        $this->rbac->addRole('bar');
        $this->rbac->addRole($foo);
        $this->rbac->addRole('snafu');

        // check that the container has the same object $foo
        $this->assertTrue($this->rbac->hasRole($foo));

        // check that the container has the same string "bar"
        $this->assertTrue($this->rbac->hasRole('bar'));

        // check that the container do not have the string "baz"
        $this->assertFalse($this->rbac->hasRole('baz'));

        // check that we can compare two different objects with same name
        $this->assertNotEquals($this->rbac->getRole('snafu'), $snafu);
        $this->assertTrue($this->rbac->hasRole($snafu));
    }

    public function testAddRoleFromString()
    {
        $this->rbac->addRole('foo');

        $foo = $this->rbac->getRole('foo');
        $this->assertInstanceOf('Zend\Permissions\Rbac\Role', $foo);
    }

    public function testAddRoleFromClass()
    {
        $foo = new Rbac\Role('foo');

        $this->rbac->addRole('foo');
        $foo2 = $this->rbac->getRole('foo');

        $this->assertEquals($foo, $foo2);
        $this->assertInstanceOf('Zend\Permissions\Rbac\Role', $foo2);
    }

    public function testAddRoleWithParentsUsingRbac()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $this->rbac->addRole($foo);
        $this->rbac->addRole($bar, $foo);

        $this->assertEquals($bar->getParent(), $foo);
        $this->assertEquals(1, count($foo->getChildren()));
    }

    public function testAddRoleWithAutomaticParentsUsingRbac()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $this->rbac->setCreateMissingRoles(true);
        $this->rbac->addRole($bar, $foo);

        $this->assertEquals($bar->getParent(), $foo);
        $this->assertEquals(1, count($foo->getChildren()));
    }

    /**
     * @tesdox Test adding custom child roles works
     */
    public function testAddCustomChildRole()
    {
        $role = $this->getMockForAbstractClass('Zend\Permissions\Rbac\RoleInterface');
        $this->rbac->setCreateMissingRoles(true)->addRole($role, array('parent'));

        $role->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('customchild'));

        $role->expects($this->once())
            ->method('hasPermission')
            ->with('test')
            ->will($this->returnValue(true));

        $this->assertTrue($this->rbac->isGranted('parent', 'test'));
    }
}
