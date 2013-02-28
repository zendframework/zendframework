<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager;

use Zend\Di\Di;
use Zend\Mvc\Service\DiFactory;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

use ZendTest\ServiceManager\TestAsset\FooCounterAbstractFactory;

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
    public function testConstructorConfig()
    {
        $config = new Config(array('services' => array('foo' => 'bar')));
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

    public function testServiceManagerIsPassedToInitializer()
    {
        $initializer = new TestAsset\FooInitializer();
        $this->serviceManager->addInitializer($initializer);
        $this->serviceManager->setFactory('foo', function () {
            return new \stdClass();
        });
        $obj = $this->serviceManager->get('foo');
        $this->assertSame($this->serviceManager, $initializer->sm);
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
    public function testSetSharedAbstractFactory()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $ret = $this->serviceManager->setShared('foo', false);
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
    public function testGetUsesIndivualSharedSettingWhenSetAndDeviatesFromShareByDefaultSetting()
    {
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setShareByDefault(false);
        $this->serviceManager->setInvokableClass('foo', 'ZendTest\ServiceManager\TestAsset\Foo');
        $this->serviceManager->setShared('foo', true);
        $this->assertSame($this->serviceManager->get('foo'), $this->serviceManager->get('foo'));

        $this->serviceManager->setShareByDefault(true);
        $this->serviceManager->setInvokableClass('foo', 'ZendTest\ServiceManager\TestAsset\Foo');
        $this->serviceManager->setShared('foo', false);
        $this->assertNotSame($this->serviceManager->get('foo'), $this->serviceManager->get('foo'));
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
        $parent->setFactory('foo', function ($sm) {
            return 'bar';
        });
        $child  = new ServiceManager();
        $child->setFactory('foo', function ($sm) {
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

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateWithMultipleAbstractFactories()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\BarAbstractFactory');
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');

        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Bar', $this->serviceManager->get('bar'));
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
        $config = new Config(array(
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
        $factory->instanceManager()->setConfig('ZendTest\ServiceManager\TestAsset\Bar', array('parameters' => array('foo' => array('a'))));
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
        $config = new Config(array(
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
        $this->serviceManager->setFactory('bar', function ($sm) {
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

    public function testShouldAllowAddingInitializersAsClassNames()
    {
        $result = $this->serviceManager->addInitializer('ZendTest\ServiceManager\TestAsset\FooInitializer');
        $this->assertSame($this->serviceManager, $result);
    }

    public function testShouldRaiseExceptionIfInitializerClassIsNotAnInitializerInterfaceImplementation()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $result = $this->serviceManager->addInitializer(get_class($this));
    }

    public function testGetGlobIteratorServiceWorksProperly()
    {
        $config = new Config(array(
            'invokables' => array(
                'foo' => 'ZendTest\ServiceManager\TestAsset\GlobIteratorService',
            ),
        ));
        $serviceManager = new ServiceManager($config);
        $foo = $serviceManager->get('foo');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\GlobIteratorService', $foo);
    }

    public function duplicateService()
    {
        $self = $this;

        return array(
            array(
                'setFactory',
                function ($services) use ($self) {
                    return $self;
                },
                $self,
                'assertSame',
            ),
            array(
                'setInvokableClass',
                'stdClass',
                'stdClass',
                'assertInstanceOf',
            ),
            array(
                'setService',
                $self,
                $self,
                'assertSame',
            ),
        );
    }

    /**
     * @dataProvider duplicateService
     */
    public function testWithAllowOverrideOnRegisteringAServiceDuplicatingAnExistingAliasShouldInvalidateTheAlias($method, $service, $expected, $assertion = 'assertSame')
    {
        $this->serviceManager->setAllowOverride(true);
        $sm = $this->serviceManager;
        $this->serviceManager->setFactory('http.response', function () use ($sm) {
            return $sm;
        });
        $this->serviceManager->setAlias('response', 'http.response');
        $this->assertSame($sm, $this->serviceManager->get('response'));

        $this->serviceManager->{$method}('response', $service);
        $this->{$assertion}($expected, $this->serviceManager->get('response'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::canonicalizeName
     */
    public function testCanonicalizeName()
    {
        $this->serviceManager->setService('foo_bar', new \stdClass());
        $this->assertEquals(true, $this->serviceManager->has('foo_bar'));
        $this->assertEquals(true, $this->serviceManager->has('foobar'));
        $this->assertEquals(true, $this->serviceManager->has('foo-bar'));
        $this->assertEquals(true, $this->serviceManager->has('foo/bar'));
        $this->assertEquals(true, $this->serviceManager->has('foo bar'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::canCreateFromAbstractFactory
     */
    public function testWanCreateFromAbstractFactoryWillNotInstantiateAbstractFactoryOnce()
    {
        $count = FooCounterAbstractFactory::$instantiationCount;
        $this->serviceManager->addAbstractFactory(__NAMESPACE__ . '\TestAsset\FooCounterAbstractFactory');

        $this->serviceManager->canCreateFromAbstractFactory('foo', 'foo');
        $this->serviceManager->canCreateFromAbstractFactory('foo', 'foo');

        $this->assertSame($count + 1, FooCounterAbstractFactory::$instantiationCount);
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::canCreateFromAbstractFactory
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testAbstractFactoryNotUsedIfNotAbleToCreate()
    {
        $service = new \stdClass;

        $af1 = $this->getMock('Zend\ServiceManager\AbstractFactoryInterface');
        $af1->expects($this->any())->method('canCreateServiceWithName')->will($this->returnValue(true));
        $af1->expects($this->any())->method('createServiceWithName')->will($this->returnValue($service));

        $af2 = $this->getMock('Zend\ServiceManager\AbstractFactoryInterface');
        $af2->expects($this->any())->method('canCreateServiceWithName')->will($this->returnValue(false));
        $af2->expects($this->never())->method('createServiceWithName');

        $this->serviceManager->addAbstractFactory($af1);
        $this->serviceManager->addAbstractFactory($af2);

        $this->assertSame($service, $this->serviceManager->create('test'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setAlias
     * @covers Zend\ServiceManager\ServiceManager::get
     * @covers Zend\ServiceManager\ServiceManager::retrieveFromPeeringManager
     */
    public function testCanGetAliasedServicesFromPeeringServiceManagers()
    {
        $service   = new \stdClass();
        $peeringSm = new ServiceManager();

        $peeringSm->setService('actual-service-name', $service);
        $this->serviceManager->addPeeringServiceManager($peeringSm);

        $this->serviceManager->setAlias('alias-name', 'actual-service-name');

        $this->assertSame($service, $this->serviceManager->get('alias-name'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testDuplicateNewInstanceMultipleAbstractFactories()
    {
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setShareByDefault(false);
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\BarAbstractFactory');
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Bar', $this->serviceManager->get('bar'));
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Bar', $this->serviceManager->get('bar'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::setService
     * @covers Zend\ServiceManager\ServiceManager::get
     * @covers Zend\ServiceManager\ServiceManager::retrieveFromPeeringManagerFirst
     * @covers Zend\ServiceManager\ServiceManager::setRetrieveFromPeeringManagerFirst
     * @covers Zend\ServiceManager\ServiceManager::addPeeringServiceManager
     */
    public function testRetrieveServiceFromPeeringServiceManagerIfretrieveFromPeeringManagerFirstSetToTrueAndServiceNamesAreSame()
    {
        $foo1 = "foo1";
        $boo1 = "boo1";
        $boo2 = "boo2";

        $this->serviceManager->setService($foo1, $boo1);
        $this->assertEquals($this->serviceManager->get($foo1), $boo1);

        $serviceManagerChild = new ServiceManager();
        $serviceManagerChild->setService($foo1, $boo2);
        $this->assertEquals($serviceManagerChild->get($foo1), $boo2);

        $this->assertFalse($this->serviceManager->retrieveFromPeeringManagerFirst());
        $this->serviceManager->setRetrieveFromPeeringManagerFirst(true);
        $this->assertTrue($this->serviceManager->retrieveFromPeeringManagerFirst());

        $this->serviceManager->addPeeringServiceManager($serviceManagerChild);

        $this->assertContains($serviceManagerChild, $this->readAttribute($this->serviceManager, 'peeringServiceManagers'));

        $this->assertEquals($serviceManagerChild->get($foo1), $boo2);
        $this->assertEquals($this->serviceManager->get($foo1), $boo2);
    }
}
