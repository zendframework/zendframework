<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Di\Di;
use Zend\Mvc\Service\DiFactory;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use ZendTest\ServiceManager\TestAsset\FooCounterAbstractFactory;
use ZendTest\ServiceManager\TestAsset\MockSelfReturningDelegatorFactory;

/**
 * @group Zend_ServiceManager
 */
class ServiceManagerTest extends TestCase
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
    public function testGetAbstractFactoryWithAlias()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $this->serviceManager->setAlias('foo', 'ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
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

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateTheSameServiceWithMultipleAbstractFactories()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooFakeAbstractFactory');
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');

        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\Foo', $this->serviceManager->get('foo'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     */
    public function testCreateTheSameServiceWithMultipleAbstractFactoriesReversePriority()
    {
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooAbstractFactory');
        $this->serviceManager->addAbstractFactory('ZendTest\ServiceManager\TestAsset\FooFakeAbstractFactory');

        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooFake', $this->serviceManager->get('foo'));
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

    public function testHasReturnsFalseOnNonStringsAndArrays()
    {
        $obj = new \stdClass();
        $this->assertFalse($this->serviceManager->has($obj));
    }

    public function testHasAcceptsArrays()
    {
        $this->serviceManager->setInvokableClass('foobar', 'foo');
        $this->assertTrue($this->serviceManager->has(array('foobar', 'foo_bar')));
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

    /**
     * When failing, this test will trigger a fatal error: Allowed memory size of # bytes exhausted
     */
    public function testCallingANonExistingServiceFromAnAbstractServiceDoesNotMakeTheServerExhaustTheAllowedMemoryByCallingItselfForTheGivenService()
    {
        $abstractFactory = new TestAsset\TrollAbstractFactory;
        $this->serviceManager->addAbstractFactory($abstractFactory);

        $this->assertSame($abstractFactory->inexistingServiceCheckResult, null);

        // By doing this the Service Manager will rely on the Abstract Service Factory
        $service = $this->serviceManager->get('SomethingThatCanBeCreated');

        $this->assertSame(false, $abstractFactory->inexistingServiceCheckResult);

        $this->assertInstanceOf('stdClass', $service);
    }

    public function testMultipleAbstractFactoriesWithOneLookingForANonExistingServiceDuringCanCreate()
    {
        $abstractFactory = new TestAsset\TrollAbstractFactory;
        $anotherAbstractFactory = $this->getMock('Zend\ServiceManager\AbstractFactoryInterface');
        $anotherAbstractFactory
            ->expects($this->exactly(2))
            ->method('canCreateServiceWithName')
            ->with(
                $this->serviceManager,
                $this->logicalOr('somethingthatcanbecreated', 'nonexistingservice'),
                $this->logicalOr('SomethingThatCanBeCreated', 'NonExistingService')
            )
            ->will($this->returnValue(false));

        $this->serviceManager->addAbstractFactory($abstractFactory);
        $this->serviceManager->addAbstractFactory($anotherAbstractFactory);

        $this->assertTrue($this->serviceManager->has('SomethingThatCanBeCreated'));
        $this->assertFalse($abstractFactory->inexistingServiceCheckResult);
    }

    public function testWaitingAbstractFactory()
    {
        $abstractFactory = new TestAsset\WaitingAbstractFactory;
        $this->serviceManager->addAbstractFactory($abstractFactory);

        $abstractFactory->waitingService = null;
        $abstractFactory->canCreateCallCount = 0;
        $this->assertFalse($this->serviceManager->has('SomethingThatCanBeCreated'));
        $this->assertEquals(1, $abstractFactory->canCreateCallCount);

        $abstractFactory->waitingService = 'SomethingThatCanBeCreated';
        $abstractFactory->canCreateCallCount = 0;
        $this->assertTrue($this->serviceManager->has('SomethingThatCanBeCreated'));
        $this->assertEquals(1, $abstractFactory->canCreateCallCount);

        $abstractFactory->canCreateCallCount = 0;
        $this->assertInstanceOf('stdClass', $this->serviceManager->get('SomethingThatCanBeCreated'));
        $this->assertEquals(1, $abstractFactory->canCreateCallCount);
    }

    public function testWaitingAbstractFactoryNestedContextCounterWhenThrowException()
    {
        $abstractFactory = new TestAsset\WaitingAbstractFactory;
        $this->serviceManager->addAbstractFactory($abstractFactory);

        $contextCounter = new \ReflectionProperty($this->serviceManager, 'nestedContextCounter');
        $contextCounter->setAccessible(true);
        $contextCounter->getValue($this->serviceManager);

        $abstractFactory->waitName = 'SomethingThatCanBeCreated';
        $abstractFactory->createNullService = true;
        $this->assertEquals(-1, $contextCounter->getValue($this->serviceManager));
        try {
            $this->serviceManager->get('SomethingThatCanBeCreated');
            $this->fail('serviceManager shoud throw Zend\ServiceManager\Exception\ServiceNotFoundException');
        } catch (\Exception $e) {
            if (stripos(get_class($e), 'PHPUnit') !== false) {
                throw $e;
            }
            $this->assertEquals(-1, $contextCounter->getValue($this->serviceManager));
        }

        $abstractFactory->createNullService = false;
        $abstractFactory->throwExceptionWhenCreate = true;
        try {
            $this->serviceManager->get('SomethingThatCanBeCreated');
            $this->fail('serviceManager shoud throw Zend\ServiceManager\Exception\ServiceNotCreatedException');
        } catch (\Exception $e) {
            if (stripos(get_class($e), 'PHPUnit') !== false) {
                throw $e;
            }
            $this->assertEquals(-1, $contextCounter->getValue($this->serviceManager));
        }
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

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorFromFactory
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorCallback
     * @covers Zend\ServiceManager\ServiceManager::addDelegator
     */
    public function testUsesDelegatorWhenAvailable()
    {
        $delegator = $this->getMock('Zend\\ServiceManager\\DelegatorFactoryInterface');

        $this->serviceManager->setService('foo-delegator', $delegator);
        $this->serviceManager->addDelegator('foo-service', 'foo-delegator');
        $this->serviceManager->setInvokableClass('foo-service', 'stdClass');

        $delegator
            ->expects($this->once())
            ->method('createDelegatorWithName')
            ->with(
                $this->serviceManager,
                'fooservice',
                'foo-service',
                $this->callback(function ($callback) {
                    if (!is_callable($callback)) {
                        return false;
                    }

                    $service = call_user_func($callback);

                    return $service instanceof \stdClass;
                })
            )
            ->will($this->returnValue($delegator));

        $this->assertSame($delegator, $this->serviceManager->create('foo-service'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorFromFactory
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorCallback
     * @covers Zend\ServiceManager\ServiceManager::addDelegator
     */
    public function testUsesMultipleDelegates()
    {
        $fooDelegator = new MockSelfReturningDelegatorFactory();
        $barDelegator = new MockSelfReturningDelegatorFactory();

        $this->serviceManager->setService('foo-delegate', $fooDelegator);
        $this->serviceManager->setService('bar-delegate', $barDelegator);
        $this->serviceManager->addDelegator('foo-service', 'foo-delegate');
        $this->serviceManager->addDelegator('foo-service', 'bar-delegate');
        $this->serviceManager->setInvokableClass('foo-service', 'stdClass');

        $this->assertSame($barDelegator, $this->serviceManager->create('foo-service'));
        $this->assertCount(1, $barDelegator->instances);
        $this->assertCount(1, $fooDelegator->instances);
        $this->assertInstanceOf('stdClass', array_shift($fooDelegator->instances));
        $this->assertSame($fooDelegator, array_shift($barDelegator->instances));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::resolveAlias
     */
    public function testSetCircularAliasReferenceThrowsException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\CircularReferenceException');

        // Only affects service managers that allow overwriting definitions
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setInvokableClass('foo-service', 'stdClass');
        $this->serviceManager->setAlias('foo-alias', 'foo-service');
        $this->serviceManager->setAlias('bar-alias', 'foo-alias');
        $this->serviceManager->setAlias('baz-alias', 'bar-alias');

        // This will now cause a cyclic reference and should throw an exception
        $this->serviceManager->setAlias('foo-alias', 'bar-alias');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::checkForCircularAliasReference
     */
    public function testResolveCircularAliasReferenceThrowsException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\CircularReferenceException');

        // simulate an inconsistent state of $servicemanager->aliases as it could be
        // caused by derived classes
        $cyclicAliases = array(
            'fooalias' => 'bazalias',
            'baralias' => 'fooalias',
            'bazalias' => 'baralias'
        );

        $reflection = new \ReflectionObject($this->serviceManager);
        $propertyReflection = $reflection->getProperty('aliases');
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($this->serviceManager, $cyclicAliases);

        // This should throw the exception
        $this->serviceManager->get('baz-alias');
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorFromFactory
     */
    public function testDelegatorFactoryWhenNotRegisteredAsService()
    {
        $delegator = $this->getMock('Zend\\ServiceManager\\DelegatorFactoryInterface');

        $this->serviceManager->addDelegator('foo-service', $delegator);
        $this->serviceManager->setInvokableClass('foo-service', 'stdClass');

        $delegator
            ->expects($this->once())
            ->method('createDelegatorWithName')
            ->with(
                $this->serviceManager,
                'fooservice',
                'foo-service',
                $this->callback(function ($callback) {
                    if (!is_callable($callback)) {
                        return false;
                    }

                    $service = call_user_func($callback);

                    return $service instanceof \stdClass;
                })
            )
            ->will($this->returnValue($delegator));

        $this->assertSame($delegator, $this->serviceManager->create('foo-service'));
    }

    /**
     * @covers Zend\ServiceManager\ServiceManager::create
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorFromFactory
     * @covers Zend\ServiceManager\ServiceManager::createDelegatorCallback
     * @covers Zend\ServiceManager\ServiceManager::addDelegator
     */
    public function testMultipleDelegatorFactoriesWhenNotRegisteredAsServices()
    {
        $fooDelegator = new MockSelfReturningDelegatorFactory();
        $barDelegator = new MockSelfReturningDelegatorFactory();

        $this->serviceManager->addDelegator('foo-service', $fooDelegator);
        $this->serviceManager->addDelegator('foo-service', $barDelegator);
        $this->serviceManager->setInvokableClass('foo-service', 'stdClass');

        $this->assertSame($barDelegator, $this->serviceManager->create('foo-service'));
        $this->assertCount(1, $barDelegator->instances);
        $this->assertCount(1, $fooDelegator->instances);
        $this->assertInstanceOf('stdClass', array_shift($fooDelegator->instances));
        $this->assertSame($fooDelegator, array_shift($barDelegator->instances));
    }

    public function testInvalidDelegatorFactoryThrowsException()
    {
        $delegatorFactory = new \stdClass;
        $this->serviceManager->addDelegator('foo-service', $delegatorFactory);

        try {
            $this->serviceManager->create('foo-service');
            $this->fail('Expected exception was not raised');
        } catch (Exception\ServiceNotCreatedException $expected) {
            $this->assertRegExp('/invalid factory/', $expected->getMessage());
            return;
        }
    }

    public function testInvalidDelegatorFactoryAmongMultipleOnesThrowsException()
    {
        $this->serviceManager->addDelegator('foo-service', new MockSelfReturningDelegatorFactory());
        $this->serviceManager->addDelegator('foo-service', new MockSelfReturningDelegatorFactory());
        $this->serviceManager->addDelegator('foo-service', 'stdClass');

        try {
            $this->serviceManager->create('foo-service');
            $this->fail('Expected exception was not raised');
        } catch (Exception\ServiceNotCreatedException $expected) {
            $this->assertRegExp('/invalid factory/', $expected->getMessage());
            return;
        }
    }

    public function testDelegatorFromCallback()
    {
        $realService = $this->getMock('stdClass', array(), array(), 'RealService');
        $delegator = $this->getMock('stdClass', array(), array(), 'Delegator');

        $delegatorFactoryCallback = function ($serviceManager, $cName, $rName, $callback) use ($delegator) {
            $delegator->real = call_user_func($callback);
            return $delegator;
        };

        $this->serviceManager->setFactory('foo-service', function () use ($realService) { return $realService; });
        $this->serviceManager->addDelegator('foo-service', $delegatorFactoryCallback);

        $service = $this->serviceManager->create('foo-service');

        $this->assertSame($delegator, $service);
        $this->assertSame($realService, $service->real);
    }

    /**
     * @dataProvider getServiceOfVariousTypes
     * @param $service
     */
    public function testAbstractFactoriesCanReturnAnyTypeButNull($service)
    {
        $abstractFactory = $this->getMock('Zend\ServiceManager\AbstractFactoryInterface');
        $abstractFactory
            ->expects($this->any())
            ->method('canCreateServiceWithName')
            ->with($this->serviceManager, 'something', 'something')
            ->will($this->returnValue(true));

        $abstractFactory
            ->expects($this->any())
            ->method('createServiceWithName')
            ->with($this->serviceManager, 'something', 'something')
            ->will($this->returnValue($service));

        $this->serviceManager->addAbstractFactory($abstractFactory);

        if ($service === null) {
            try {
                $this->serviceManager->get('something');
                $this->fail('ServiceManager::get() successfully returned null');
            } catch (\Exception $e) {
                $this->assertInstanceOf('Zend\ServiceManager\Exception\ServiceNotCreatedException', $e);
            }
        } else {
            $this->assertSame($service, $this->serviceManager->get('something'));
        }
    }

    /**
     * @dataProvider getServiceOfVariousTypes
     * @param $service
     */
    public function testFactoriesCanReturnAnyTypeButNull($service)
    {
        $factory = function () use ($service) {
            return $service;
        };
        $this->serviceManager->setFactory('something', $factory);

        if ($service === null) {
            try {
                $this->serviceManager->get('something');
                $this->fail('ServiceManager::get() successfully returned null');
            } catch (\Exception $e) {
                $this->assertInstanceOf('Zend\ServiceManager\Exception\ServiceNotCreatedException', $e);
            }
        } else {
            $this->assertSame($service, $this->serviceManager->get('something'));
        }
    }

    /**
     * @dataProvider getServiceOfVariousTypes
     * @param $service
     */
    public function testServicesCanBeOfAnyTypeButNull($service)
    {
        $this->serviceManager->setService('something', $service);

        if ($service === null) {
            try {
                $this->serviceManager->get('something');
                $this->fail('ServiceManager::get() successfully returned null');
            } catch (\Exception $e) {
                $this->assertInstanceOf('Zend\ServiceManager\Exception\ServiceNotFoundException', $e);
            }
        } else {
            $this->assertSame($service, $this->serviceManager->get('something'));
        }
    }

    public function getServiceOfVariousTypes()
    {
        return array(
            array(null),
            array('string'),
            array(1),
            array(1.2),
            array(array()),
            array(function () {}),
            array(false),
            array(new \stdClass()),
            array(tmpfile())
        );
    }

    /**
     * @group ZF2-4377
     */
    public function testServiceManagerRespectsSharedFlagWhenRetrievingFromPeeredServiceManager()
    {
        $this->serviceManager->setInvokableClass('foo', 'ZendTest\ServiceManager\TestAsset\Foo');
        $this->serviceManager->setShared('foo', false);

        $childManager = new ServiceManager(new Config());
        $childManager->addPeeringServiceManager($this->serviceManager);
        $childManager->setRetrieveFromPeeringManagerFirst(false);

        $this->assertNotSame($childManager->get('foo'), $childManager->get('foo'));
    }

    /**
     * @group ZF2-4377
     */
    public function testIsSharedThrowsExceptionWhenPassedNameWhichDoesNotExistAnywhere()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->serviceManager->isShared('foobarbazbat');
    }

    public function testPeeringServiceManagersInBothDirectionsDontRunIntoInfiniteLoop()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $peeredServiceManager = $this->serviceManager->createScopedServiceManager(ServiceManager::SCOPE_CHILD);
        $peeredServiceManager->addPeeringServiceManager($this->serviceManager);
        $this->serviceManager->get('foobarbazbat');
    }

    public function testServiceCanBeFoundFromPeeringServicesManagers()
    {
        $peeredServiceManager = new ServiceManager();
        $peeredServiceManager->addPeeringServiceManager($this->serviceManager);
        $this->serviceManager->addPeeringServiceManager($peeredServiceManager);

        $secondParentServiceManager = new ServiceManager();
        $secondParentServiceManager->addPeeringServiceManager($peeredServiceManager);
        $peeredServiceManager->addPeeringServiceManager($secondParentServiceManager);

        $expectedService = new \stdClass();
        $secondParentServiceManager->setService('peered_service', $expectedService);

        // check if service is direct child of secong parent service manager
        $this->assertFalse($this->serviceManager->has('peered_service', true, false));
        $this->assertFalse($peeredServiceManager->has('peered_service', true, false));
        $this->assertTrue($secondParentServiceManager->has('peered_service', true, false));

        // check if we can receive service from peered service managers
        $this->assertTrue($this->serviceManager->has('peered_service'));
        $this->assertTrue($peeredServiceManager->has('peered_service'));
        $this->assertTrue($secondParentServiceManager->has('peered_service'));
        $this->assertSame($expectedService, $this->serviceManager->get('peered_service'));
        $this->assertSame($expectedService, $peeredServiceManager->get('peered_service'));
        $this->assertSame($expectedService, $secondParentServiceManager->get('peered_service'));
    }
}
