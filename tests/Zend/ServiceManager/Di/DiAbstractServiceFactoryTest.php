<?php

namespace ZendTest\ServiceManager\Di;

use Zend\ServiceManager\Di\DiAbstractServiceFactory,
Zend\ServiceManager\Di\DiInstanceManagerProxy;

class DiAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DiAbstractServiceFactory
     */
    protected $diAbstractServiceFactory = null;

    /**@#+
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDi = null;
    protected $mockServiceLocator = null;
    /**@#-*/

    protected $fooInstance = null;

    public function setup()
    {
        $instanceManager = new \Zend\Di\InstanceManager();
        $instanceManager->addSharedInstance($this->fooInstance = new \stdClass(), 'foo');
        $this->mockDi = $this->getMock('Zend\Di\Di', array(), array(null, $instanceManager));
        $this->mockServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->diAbstractServiceFactory = new DiAbstractServiceFactory(
            $this->mockDi
        );
    }


    /**
     * @covers Zend\ServiceManager\Di\DiAbstractServiceFactory::__construct
     */
    public function testConstructor()
    {
        $instance = new DiAbstractServiceFactory(
            $this->getMock('Zend\Di\Di')
        );
        $this->assertInstanceOf('Zend\ServiceManager\Di\DiAbstractServiceFactory', $instance);
    }

    /**
     * @covers Zend\ServiceManager\Di\DiAbstractServiceFactory::createServiceWithName
     * @covers Zend\ServiceManager\Di\DiAbstractServiceFactory::get
     */
    public function testCreateServiceWithName()
    {
        $foo = $this->diAbstractServiceFactory->createServiceWithName($this->mockServiceLocator, 'foo');
        $this->assertEquals($this->fooInstance, $foo);
    }
}
