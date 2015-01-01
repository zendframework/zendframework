<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\EventManager\StaticEventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Controller\Plugin\Forward as ForwardPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\CallbackHandler;
use ZendTest\Mvc\Controller\TestAsset\ForwardController;
use ZendTest\Mvc\Controller\TestAsset\SampleController;
use ZendTest\Mvc\Controller\TestAsset\UneventfulController;
use ZendTest\Mvc\TestAsset\Locator;

class ForwardTest extends TestCase
{
    /**
     * @var PluginManager
     */
    private $plugins;

    /**
     * @var ControllerManager
     */
    private $controllers;

    /**
     * @var SampleController
     */
    private $controller;

    /**
     * @var \Zend\Mvc\Controller\Plugin\Forward
     */
    private $plugin;

    public function setUp()
    {
        StaticEventManager::resetInstance();

        $mockSharedEventManager = $this->getMock('Zend\EventManager\SharedEventManagerInterface');
        $mockSharedEventManager->expects($this->any())->method('getListeners')->will($this->returnValue(array()));
        $mockEventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $mockEventManager->expects($this->any())->method('getSharedManager')->will($this->returnValue($mockSharedEventManager));
        $mockApplication = $this->getMock('Zend\Mvc\ApplicationInterface');
        $mockApplication->expects($this->any())->method('getEventManager')->will($this->returnValue($mockEventManager));

        $event   = new MvcEvent();
        $event->setApplication($mockApplication);
        $event->setRequest(new Request());
        $event->setResponse(new Response());

        $routeMatch = new RouteMatch(array('action' => 'test'));
        $routeMatch->setMatchedRouteName('some-route');
        $event->setRouteMatch($routeMatch);

        $services    = new Locator();
        $plugins     = $this->plugins = new PluginManager();
        $plugins->setServiceLocator($services);

        $controllers = $this->controllers = new ControllerManager();
        $controllers->setFactory('forward', function () use ($plugins) {
            $controller = new ForwardController();
            $controller->setPluginManager($plugins);
            return $controller;
        });
        $controllers->setServiceLocator($services);
        $controllerLoader = function () use ($controllers) {
            return $controllers;
        };
        $services->add('ControllerLoader', $controllerLoader);
        $services->add('ControllerManager', $controllerLoader);
        $services->add('ControllerPluginManager', function () use ($plugins) {
            return $plugins;
        });
        $services->add('Zend\ServiceManager\ServiceLocatorInterface', function () use ($services) {
            return $services;
        });
        $services->add('EventManager', function () use ($mockEventManager) {
            return $mockEventManager;
        });
        $services->add('SharedEventManager', function () use ($mockSharedEventManager) {
            return $mockSharedEventManager;
        });

        $this->controller = new SampleController();
        $this->controller->setEvent($event);
        $this->controller->setServiceLocator($services);
        $this->controller->setPluginManager($plugins);

        $this->plugin = $this->controller->plugin('forward');
    }

    public function tearDown()
    {
        StaticEventManager::resetInstance();
    }

    public function testPluginWithoutEventAwareControllerRaisesDomainException()
    {
        $controller = new UneventfulController();
        $plugin     = new ForwardPlugin($this->controllers);
        $plugin->setController($controller);
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'InjectApplicationEventInterface');
        $plugin->dispatch('forward');
    }

    public function testPluginWithoutControllerLocatorRaisesServiceNotCreatedException()
    {
        $controller = new SampleController();
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotCreatedException');
        $plugin     = $controller->plugin('forward');
    }

    public function testDispatchRaisesDomainExceptionIfDiscoveredControllerIsNotDispatchable()
    {
        $locator = $this->controller->getServiceLocator();
        $locator->add('bogus', function () {
            return new stdClass;
        });
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->plugin->dispatch('bogus');
    }

    public function testDispatchRaisesDomainExceptionIfCircular()
    {
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'Circular forwarding');
        $sampleController = $this->controller;
        $this->controllers->setService('sample', $sampleController);
        $this->plugin->dispatch('sample', array('action' => 'test-circular'));
    }

    public function testPluginDispatchsRequestedControllerWhenFound()
    {
        $result = $this->plugin->dispatch('forward');
        $this->assertInternalType('array', $result);
        $this->assertEquals(array('content' => 'ZendTest\Mvc\Controller\TestAsset\ForwardController::testAction'), $result);
    }

    /**
     * @group 5432
     */
    public function testNonArrayListenerDoesNotRaiseErrorWhenPluginDispatchsRequestedController()
    {
        $services = $this->plugins->getServiceLocator();
        $events   = $services->get('EventManager');
        $sharedEvents = $this->getMock('Zend\EventManager\SharedEventManagerInterface');
        $sharedEvents->expects($this->any())->method('getListeners')->will($this->returnValue(array(
            new CallbackHandler(function ($e) {})
        )));
        $events = $this->getMock('Zend\EventManager\EventManagerInterface');
        $events->expects($this->any())->method('getSharedManager')->will($this->returnValue($sharedEvents));
        $application = $this->getMock('Zend\Mvc\ApplicationInterface');
        $application->expects($this->any())->method('getEventManager')->will($this->returnValue($events));
        $event = $this->controller->getEvent();
        $event->setApplication($application);

        $result = $this->plugin->dispatch('forward');
        $this->assertInternalType('array', $result);
        $this->assertEquals(array('content' => 'ZendTest\Mvc\Controller\TestAsset\ForwardController::testAction'), $result);
    }

    public function testDispatchWillSeedRouteMatchWithPassedParameters()
    {
        $result = $this->plugin->dispatch('forward', array(
            'action' => 'test-matches',
            'param1' => 'foobar',
        ));
        $this->assertInternalType('array', $result);
        $this->assertTrue(isset($result['action']));
        $this->assertEquals('test-matches', $result['action']);
        $this->assertTrue(isset($result['param1']));
        $this->assertEquals('foobar', $result['param1']);
    }

    public function testRouteMatchObjectRemainsSameFollowingForwardDispatch()
    {
        $routeMatch            = $this->controller->getEvent()->getRouteMatch();
        $matchParams           = $routeMatch->getParams();
        $matchMatchedRouteName = $routeMatch->getMatchedRouteName();
        $result = $this->plugin->dispatch('forward', array(
            'action' => 'test-matches',
            'param1' => 'foobar',
        ));
        $testMatch            = $this->controller->getEvent()->getRouteMatch();
        $testParams           = $testMatch->getParams();
        $testMatchedRouteName = $testMatch->getMatchedRouteName();

        $this->assertSame($routeMatch, $testMatch);
        $this->assertEquals($matchParams, $testParams);
        $this->assertEquals($matchMatchedRouteName, $testMatchedRouteName);
    }

    public function testAllowsPassingEmptyArrayOfRouteParams()
    {
        $result = $this->plugin->dispatch('forward', array());
        $this->assertInternalType('array', $result);
        $this->assertTrue(isset($result['status']));
        $this->assertEquals('not-found', $result['status']);
        $this->assertTrue(isset($result['params']));
        $this->assertEquals(array(), $result['params']);
    }

    /**
     * @group 6398
     */
    public function testSetListenersToDetachIsFluent()
    {
        $this->assertSame($this->plugin, $this->plugin->setListenersToDetach(array()));
    }
}
