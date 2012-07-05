<?php

namespace ZendTest\ServiceManager;

use Zend\Di\Di;
use Zend\Mvc\Service\DiFactory;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Configuration;

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
     * @covers Zend\ServiceManager\ServiceManager::__construct
     */
    public function testConstructorConfiguration()
    {
        $config = new Configuration(array('services' => array('foo' => 'bar')));
        $serviceManager = new ServiceManager($config);
        $this->assertEquals('bar', $serviceManager->get('foo'));
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
     * @covers Zend\ServiceManager\ServiceManager::setThrowExceptionInCreate
     * @covers Zend\ServiceManager\ServiceManager::getThrowExceptionInCreate
     */
    public function testThrowExceptionInCreate()
    {
        $this->assertTrue($this->serviceManager->getThrowExceptionInCreate());
        $ret = $this->serviceManager->setThrowExceptionInCreate(false);
        $this->assertSame($this->serviceManager, $ret);
        $this->assertFalse($this->serviceManager->getThrowExceptionInCreate());
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
     * @covers Zend\ServiceManager\ServiceManager::addAbstractFactory
     */
    public function testAddAbstractFactory()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');

        $ret = $this->serviceManager->addAbstractFactory(new TestAsset\FooAbstractFactory());
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::addAbstractFactory
     */
    public function testAddAbstractFactoryThrowsExceptionOnInvalidFactory()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $this->serviceManager->addAbstractFactory(10);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::addInitializer
     */
    public function testAddInitializer()
    {
        $ret = $this->serviceManager->addInitializer(new TestAsset\FooInitializer());
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::addInitializer
     */
    public function testAddInitializerThrowsExceptionOnInvalidInitializer()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $this->serviceManager->addInitializer(5);
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
     */
    public function testGet()
    {
        $this->serviceManager->setService('foo', 'bar');
        $this->assertEquals('bar', $this->serviceManager->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testGetDoesNotThrowExceptionOnEmptyArray()
    {
        $this->serviceManager->setService('foo', array());
        $this->serviceManager->get('foo');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testGetThrowsExceptionOnUnknownService()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->assertEquals('bar', $this->serviceManager->get('foo'));
    }


    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testGetWithAlias()
    {
        $this->serviceManager->setService('foo', 'bar');
        $this->serviceManager->setAlias('baz', 'foo');
        $this->assertEquals('bar', $this->serviceManager->get('baz'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testGetWithScopedContainer()
    {
        $this->serviceManager->setService('foo', 'bar');
        $scopedServiceManager = $this->serviceManager->createScopedServiceManager();
        $this->assertEquals('bar', $scopedServiceManager->get('foo'));
    }

    public function testCanRetrieveFromParentPeeringManager()
    {
        $parent = new ServiceManager();
        $parent->setService('foo', 'bar');
        $child  = new ServiceManager();
        $child->addPeeringServiceManager($parent, ServiceManager::SCOPE_PARENT);
        $this->assertEquals('bar', $child->get('foo'));
    }

    public function testCanRetrieveFromChildPeeringManager()
    {
        $parent = new ServiceManager();
        $child  = new ServiceManager();
        $child->addPeeringServiceManager($parent, ServiceManager::SCOPE_CHILD);
        $child->setService('foo', 'bar');
        $this->assertEquals('bar', $parent->get('foo'));
    }

    public function testAllowsRetrievingFromPeeringContainerFirst()
    {
        $parent = new ServiceManager();
        $parent->setFactory('foo', function($sm) {
            return 'bar';
        });
        $child  = new ServiceManager();
        $child->setFactory('foo', function($sm) {
            return 'baz';
        });
        $child->addPeeringServiceManager($parent, ServiceManager::SCOPE_PARENT);
        $child->setRetrieveFromPeeringManagerFirst(true);
        $this->assertEquals('bar', $child->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateWithInvokableClass()
    {
        $this->serviceManager->setInvokableClass('foo', 'ZendTest\ServiceManager\TestAsset\Foo');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateWithFactoryInstance()
    {
        $this->serviceManager->setFactory('foo', 'ZendTest\ServiceManager\TestAsset\FooFactory');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateWithCallableFactory()
    {
        $this->serviceManager->setFactory('foo', function () { return new TestAsset\Foo; });
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateWithAbstractFactory()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
    }

    public function testCreateWithInitializerObject()
    {
        $this->serviceManager->addInitializer(new TestAsset\FooInitializer(array('foo' => 'bar')));
        $this->serviceManager->setFactory('foo', function () {
            return new \stdClass();
        });
        $obj = $this->serviceManager->get('foo');
        $this->assertEquals('bar', $obj->foo);
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
     */
    public function testSetAlias()
    {
        $this->serviceManager->setInvokableClass('foo', 'bar');
        $ret = $this->serviceManager->setAlias('bar', 'foo');
        $this->assertSame($this->serviceManager, $ret);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     */
    public function testSetAliasThrowsExceptionOnInvalidAliasName()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceNameException');
        $this->serviceManager->setAlias(5, 10);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     */
    public function testSetAliasThrowsExceptionOnEmptyAliasName()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceNameException');
        $this->serviceManager->setAlias('', 'foo');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     */
    public function testSetAliasThrowsExceptionOnDuplicateAlias()
    {
        $this->serviceManager->setService('foo', 'bar');
        $this->serviceManager->setAlias('baz', 'foo');

        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceNameException');
        $this->serviceManager->setAlias('baz', 'foo');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     */
    public function testSetAliasDoesNotThrowExceptionOnServiceNotFound()
    {
        $this->serviceManager->setAlias('foo', 'bar');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testGetServiceThrowsExceptionOnAliasWithNoSetService()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->serviceManager->setAlias('foo', 'bar');
        $this->serviceManager->get('foo');
    }

    /**
     * @cover Zend\ServiceManager\ServiceManager::get
     */
    public function testGetServiceThrowsExceptionOnMultipleAliasesWithNoSetService()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->serviceManager->setAlias('foo', 'bar');
        $this->serviceManager->setAlias('baz', 'foo');
        $this->serviceManager->get('foo');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::hasAlias
     */
    public function testHasAlias()
    {
        $this->assertFalse($this->serviceManager->hasAlias('foo'));

        $this->serviceManager->setService('bar', 'baz');
        $this->serviceManager->setAlias('foo', 'bar');
        $this->assertTrue($this->serviceManager->hasAlias('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::createScopedServiceManager
     */
    public function testCreateScopedServiceManager()
    {
        $this->serviceManager->setService('foo', 'bar');
        $scopedServiceManager = $this->serviceManager->createScopedServiceManager();
        $this->assertNotSame($this->serviceManager, $scopedServiceManager);
        $this->assertFalse($scopedServiceManager->has('foo', true, false));

        $this->assertContains($this->serviceManager, $this->readAttribute($scopedServiceManager, 'peeringServiceManagers'));

        // test child scoped
        $childScopedServiceManager = $this->serviceManager->createScopedServiceManager(ServiceManager::SCOPE_CHILD);
        $this->assertContains($childScopedServiceManager, $this->readAttribute($this->serviceManager, 'peeringServiceManagers'));
    }

    public function testConfigureWithInvokableClass()
    {
        $config = new Configuration(array(
            'invokables' => array(
                'foo' => 'ZendTest\ServiceManager\TestAsset\Foo',
            ),
        ));
        $serviceManager = new ServiceManager($config);
        $foo = $serviceManager->get('foo');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $foo);
    }

    public function testPeeringService()
    {
        $di = new Di();
        $di->instanceManager()->setParameters('ZendTest\ServiceManager\TestAsset\Bar', array('foo' => array('a')));
        $this->serviceManager->addAbstractFactory(new DiAbstractServiceFactory($di));
        $sm = $this->serviceManager->createScopedServiceManager(ServiceManager::SCOPE_PARENT);
        $sm->setFactory('di', new DiFactory());
        $bar = $sm->get('ZendTest\ServiceManager\TestAsset\Bar', true);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Bar', $bar);
    }

    public function testDiAbstractServiceFactory()
    {
        $di = $this->getMock('Zend\Di\Di');
        $factory = new DiAbstractServiceFactory($di);
        $factory->instanceManager()->setConfiguration('ZendTest\ServiceManager\TestAsset\Bar', array('parameters' => array('foo' => array('a'))));
        $this->serviceManager->addAbstractFactory($factory);

        $this->assertTrue($this->serviceManager->has('ZendTest\ServiceManager\TestAsset\Bar', true));

        $bar = $this->serviceManager->get('ZendTest\ServiceManager\TestAsset\Bar', true);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Bar', $bar);
    }

    public function testExceptionThrowingFactory()
    {
        $this->serviceManager->setFactory('foo', 'ZendTest\ServiceManager\TestAsset\ExceptionThrowingFactory');
        try {
            $this->serviceManager->get('foo');
            $this->fail("No exception thrown");
        } catch (Exception\ServiceNotCreatedException $e) {
            $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooException', $e->getPrevious());
        }
    }

    /**
     * @expectedException Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testCannotUseUnknownServiceNameForAbstractFactory()
    {
        $config = new Configuration(array(
            'abstract_factories' => array(
                'ZendTest\ServiceManager\TestAsset\FooAbstractFactory',
            ),
        ));
        $serviceManager = new ServiceManager($config);
        $serviceManager->setFactory('foo', 'ZendTest\ServiceManager\TestAsset\FooFactory');
        $foo = $serviceManager->get('unknownObject');
    }

    /**
     * @expectedException Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testDoNotFallbackToAbstractFactory()
    {
        $factory = function ($sm) {
            return new TestAsset\Bar();
        };
        $serviceManager = new ServiceManager();
        $serviceManager->setFactory('ZendTest\ServiceManager\TestAsset\Bar', $factory);
        $di = new Di();
        $di->instanceManager()->setParameters('ZendTest\ServiceManager\TestAsset\Bar', array('foo' => array('a')));
        $serviceManager->addAbstractFactory(new DiAbstractServiceFactory($di));
        $bar = $serviceManager->get('ZendTest\ServiceManager\TestAsset\Bar');
    }

    /**
     * @expectedException Zend\ServiceManager\Exception\InvalidServiceNameException
     */
    public function testAssignAliasWithExistingServiceName()
    {
        $this->serviceManager->setFactory('foo', 'ZendTest\ServiceManager\TestAsset\FooFactory');
        $this->serviceManager->setFactory('bar', function ($sm)
            {
                return new Bar(array('a'));
            });
        $this->serviceManager->setAllowOverride(false);
        // should throw an exception because 'foo' already exists in the service manager
        $this->serviceManager->setAlias('foo', 'bar');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::createFromAbstractFactory
     * @covers Zend\ServiceManager\ServiceManager::has
     */
    public function testWillNotCreateCircularReferences()
    {
        $abstractFactory = new TestAsset\CircularDependencyAbstractFactory();
        $sm = new ServiceManager();
        $sm->addAbstractFactory($abstractFactory);
        $foo = $sm->get('foo');
        $this->assertSame($abstractFactory->expectedInstance, $foo);
    }
}
