<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Di;

use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;

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
        $foo = $this->diAbstractServiceFactory->createServiceWithName($this->mockServiceLocator, 'foo', 'foo');
        $this->assertEquals($this->fooInstance, $foo);
    }

    /**
     * @covers Zend\ServiceManager\Di\DiAbstractServiceFactory::canCreateServiceWithName
     */
    public function testCanCreateServiceWithName()
    {
        $instance = new DiAbstractServiceFactory($this->getMock('Zend\Di\Di'));
        $im = $instance->instanceManager();

        $locator = new ServiceManager();

        // will check shared instances
        $this->assertFalse($instance->canCreateServiceWithName($locator, 'a-shared-instance-alias', 'a-shared-instance-alias'));
        $im->addSharedInstance(new \stdClass(), 'a-shared-instance-alias');
        $this->assertTrue($instance->canCreateServiceWithName($locator, 'a-shared-instance-alias', 'a-shared-instance-alias'));

        // will check aliases
        $this->assertFalse($instance->canCreateServiceWithName($locator, 'an-alias', 'an-alias'));
        $im->addAlias('an-alias', 'stdClass');
        $this->assertTrue($instance->canCreateServiceWithName($locator, 'an-alias', 'an-alias'));

        // will check instance configurations
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Non\Existing', __NAMESPACE__ . '\Non\Existing'));
        $im->setConfig(__NAMESPACE__ . '\Non\Existing', array('parameters' => array('a' => 'b')));
        $this->assertTrue($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Non\Existing', __NAMESPACE__ . '\Non\Existing'));

        // will check preferences for abstract types
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\AbstractClass', __NAMESPACE__ . '\AbstractClass'));
        $im->setTypePreference(__NAMESPACE__ . '\AbstractClass', array(__NAMESPACE__ . '\Non\Existing'));
        $this->assertTrue($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\AbstractClass', __NAMESPACE__ . '\AbstractClass'));

        // will check definitions
        $def = $instance->definitions();
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Other\Non\Existing', __NAMESPACE__ . '\Other\Non\Existing'));
        $classDefinition = $this->getMock('Zend\Di\Definition\DefinitionInterface');
        $classDefinition
            ->expects($this->any())
            ->method('hasClass')
            ->with($this->equalTo(__NAMESPACE__ . '\Other\Non\Existing'))
            ->will($this->returnValue(true));
        $def->addDefinition($classDefinition);
        $this->assertTrue($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Other\Non\Existing', __NAMESPACE__ . '\Other\Non\Existing'));
    }
}
