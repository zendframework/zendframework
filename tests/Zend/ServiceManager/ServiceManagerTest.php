<?php

namespace ZendTest\ServiceManager;

use Zend\ServiceManager\ServiceManager;

class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;

    public function setup()
    {
        $this->serviceManager = new ServiceManager;
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAllowOverride
     * @covers Zend\ServiceManager\ServiceManager::getAllowOverride
     */
    public function testAllowOverride()
    {
        $this->assertFalse($this->serviceManager->getAllowOverride());
        $ret = $this->serviceManager->setAllowOverride(true);
        $this->assertSame($this->serviceManager, $ret);
        $this->assertTrue($this->serviceManager->getAllowOverride());
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setInvokableClass
     */
    public function testSetInvokableClass()
    {
        $ret = $this->serviceManager->setInvokableClass('foo', 'bar');
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setFactory
     */
    public function testSetFactory()
    {
        $ret = $this->serviceManager->setFactory('foo', 'bar');
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setFactory
     */
    public function testSetFactoryThrowsExceptionOnDuplicate()
    {
        $this->serviceManager->setFactory('foo', 'bar');
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceNameException');
        $this->serviceManager->setFactory('foo', 'bar');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::addAbstractSource
     * @todo   Implement testAddAbstractSource().
     */
    public function testAddAbstractSource()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::addInitializer
     * @todo   Implement testAddInitializer().
     */
    public function testAddInitializer()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setService
     */
    public function testSetService()
    {
        $ret = $this->serviceManager->setService('foo', 'bar');
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setShared
     */
    public function testSetShared()
    {
        $this->serviceManager->setInvokableClass('foo', 'bar');
        $ret = $this->serviceManager->setShared('foo', true);
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setShared
     */
    public function testSetSharedThrowsExceptionOnUnregisteredService()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->serviceManager->setShared('foo', true);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     * @todo   Implement testGet().
     */
    public function testGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     * @todo   Implement testCreate().
     */
    public function testCreate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::has
     */
    public function testHas()
    {
        $this->assertFalse($this->serviceManager->has('foo'));
        $this->serviceManager->setInvokableClass('foo', 'bar');
        $this->assertTrue($this->serviceManager->has('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     * @todo   Implement testSetAlias().
     */
    public function testSetAlias()
    {
        $this->serviceManager->setInvokableClass('foo', 'bar');
        $ret = $this->serviceManager->setAlias('bar', 'foo');
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::hasAlias
     * @todo   Implement testHasAlias().
     */
    public function testHasAlias()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::createScopedServiceManager
     * @todo   Implement testCreateScopedServiceManager().
     */
    public function testCreateScopedServiceManager()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::registerPeerInstanceManager
     * @todo   Implement testRegisterPeerInstanceManager().
     */
    public function testRegisterPeerInstanceManager()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
