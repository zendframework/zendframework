<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Registry
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest;

use \Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Registry
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Registry
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Registry::_unsetInstance();
    }

    public function tearDown()
    {
        Registry::_unsetInstance();
    }

    public function testRegistryUninitIsRegistered()
    {
        // checking entry is set returns false,
        // but does not initialize instance
        $this->assertFalse(Registry::isRegistered('objectname'));
    }

    public function testRegistryUninitGetInstance()
    {
        // getting instance initializes instance
        $registry = Registry::getInstance();
        $this->assertInstanceOf('Zend\Registry', $registry);
    }

    public function testRegistryUninitSet()
    {
        // setting value initializes instance
        Registry::set('foo', 'bar');
        $registry = Registry::getInstance();
        $this->assertInstanceOf('Zend\Registry', $registry);
    }

    public function testRegistryUninitGet()
    {
        // getting value initializes instance
        // but throws different exception because
        // entry is not registered
        try {
            Registry::get('foo');
            $this->fail('Expected exception when trying to fetch a non-existent key.');
        } catch (\RuntimeException $e) {
            $this->assertContains('No entry is registered for key', $e->getMessage());
        }
        $registry = Registry::getInstance();
        $this->assertInstanceOf('Zend\Registry', $registry);
    }

    public function testRegistrySingletonSameness()
    {
        $registry1 = Registry::getInstance();
        $registry2 = Registry::getInstance();
        $this->assertInstanceOf('Zend\Registry', $registry1);
        $this->assertInstanceOf('Zend\Registry', $registry2);
        $this->assertEquals($registry1, $registry2);
        $this->assertSame($registry1, $registry2);
    }

    public function testRegistryEqualContents()
    {
        Registry::set('foo', 'bar');
        $registry1 = Registry::getInstance();
        $registry2 = new Registry(array('foo' => 'bar'));
        $this->assertEquals($registry1, $registry2);
        $this->assertNotSame($registry1, $registry2);
    }

    public function testRegistryUnequalContents()
    {
        $registry1 = Registry::getInstance();
        $registry2 = new Registry(array('foo' => 'bar'));
        $this->assertNotEquals($registry1, $registry2);
        $this->assertNotSame($registry1, $registry2);
    }

    public function testRegistrySetAndIsRegistered()
    {
        $this->assertFalse(Registry::isRegistered('foo'));
        Registry::set('foo', 'bar');
        $this->assertTrue(Registry::isRegistered('foo'));
    }

    public function testRegistryGet()
    {
        Registry::set('foo', 'bar');
        $bar = Registry::get('foo');
        $this->assertEquals('bar', $bar);
    }

    public function testRegistryArrayObject()
    {
        $registry = Registry::getInstance();
        $registry['emptyArray'] = array();
        $registry['null'] = null;

        $this->assertTrue(isset($registry['emptyArray']));
        $this->assertTrue(isset($registry['null']));
        $this->assertFalse(isset($registry['noIndex']));

        $this->assertTrue(Registry::isRegistered('emptyArray'));
        $this->assertTrue(Registry::isRegistered('null'));
        $this->assertFalse(Registry::isRegistered('noIndex'));
    }

    public function testRegistryArrayAsProps()
    {
        $registry = new Registry(array(), \ArrayObject::ARRAY_AS_PROPS);
        $registry->foo = 'bar';
        $this->assertTrue(isset($registry->foo));
        $this->assertEquals('bar', $registry->foo);
    }

    public function testRegistryExceptionInvalidClassname()
    {
        try {
            $registry = Registry::setClassName(new \stdClass());
            $this->fail('Expected exception, because setClassName() wants a string');
        } catch (\RuntimeException $e) {
            $this->assertContains('Argument is not a class name', $e->getMessage());
        }
    }

    /**
     * NB: We cannot make a unit test for the class not Zend_Registry or
     * a subclass, because that is enforced by type-hinting in the
     * Zend_Registry::setInstance() method. Type-hinting violations throw
     * an error, not an exception, so it cannot be caught in a unit test.
     */

    public function testRegistryExceptionNoEntry()
    {
        try {
            $foo = Registry::get('foo');
            $this->fail('Expected exception when trying to fetch a non-existent key.');
        } catch (\RuntimeException $e) {
            $this->assertContains('No entry is registered for key', $e->getMessage());
        }
    }

    public function testRegistryExceptionAlreadyInitialized()
    {
        $registry = Registry::getInstance();

        try {
            Registry::setClassName('anyclass');
            $this->fail('Expected exception, because we cannot initialize the registry if it is already initialized.');
        } catch (\RuntimeException $e) {
            $this->assertContains('Registry is already initialized', $e->getMessage());
        }
        try {
            Registry::setInstance(new Registry());
            $this->fail('Expected exception, because we cannot initialize the registry if it is already initialized.');
        } catch (\RuntimeException $e) {
            $this->assertContains('Registry is already initialized', $e->getMessage());
        }
    }

    public function testRegistryExceptionClassNotFound()
    {
        try {
            $registry = @Registry::setClassName('classdoesnotexist');
            $this->fail('Expected exception, because we cannot initialize the registry using a non-existent class.');
        } catch (\Zend\Loader\Exception $e) {
            $this->assertRegExp('/file .* does not exist or .*/i', $e->getMessage());
        }
    }

    public function testDefaultRegistryArrayAsPropsZF4654()
    {
        $registry = Registry::getInstance();
        $registry->bar = "baz";
        $this->assertEquals('baz', Registry::get('bar'));
    }
}
