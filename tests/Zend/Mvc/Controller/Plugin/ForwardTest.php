<?php

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Controller\Plugin\Forward as ForwardPlugin,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    ZendTest\Mvc\Controller\TestAsset\ForwardController,
    ZendTest\Mvc\Controller\TestAsset\SampleController,
    ZendTest\Mvc\Controller\TestAsset\UneventfulController,
    ZendTest\Mvc\Controller\TestAsset\UnlocatableEventfulController,
    ZendTest\Mvc\TestAsset\Locator;

class ForwardTest extends TestCase
{
    public function setUp()
    {
        $event   = new MvcEvent();
        $event->setRequest(new Request());
        $event->setResponse(new Response());
        $event->setRouteMatch(new RouteMatch(array('action' => 'test')));

        $locator = new Locator;
        $locator->add('forward', function() {
            return new ForwardController();
        });

        $this->controller = new SampleController();
        $this->controller->setEvent($event);
        $this->controller->setServiceLocator($locator);

        $this->plugin = $this->controller->plugin('forward');
    }

    public function testPluginWithoutEventAwareControllerRaisesDomainException()
    {
        $controller = new UneventfulController();
        $plugin     = new ForwardPlugin();
        $plugin->setController($controller);
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'InjectApplicationEventInterface');
        $plugin->dispatch('forward');
    }

    public function testPluginWithoutLocatorAwareControllerRaisesDomainException()
    {
        $controller = new UnlocatableEventfulController();
        $controller->setEvent($this->controller->getEvent());
        $plugin     = new ForwardPlugin();
        $plugin->setController($controller);
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'implements ServiceLocatorAwareInterface');
        $plugin->dispatch('forward');
    }

    public function testPluginWithoutControllerLocatorRaisesDomainException()
    {
        $controller = new SampleController();
        $plugin     = $controller->plugin('forward');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'composes Locator');
        $plugin->dispatch('forward');
    }

    public function testDispatchRaisesDomainExceptionIfDiscoveredControllerIsNotDispatchable()
    {
        $locator = $this->controller->getServiceLocator();
        $locator->add('bogus', function() {
            return new stdClass;
        });
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'DispatchableInterface');
        $this->plugin->dispatch('bogus');
    }

    public function testDispatchRaisesDomainExceptionIfCircular()
    {
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'Circular forwarding');
        $sampleController = $this->controller;
        $sampleController->getServiceLocator()->add('sample', function() use ($sampleController) {
            return $sampleController;
        });
        $this->plugin->dispatch('sample', array('action' => 'test-circular'));
    }

    public function testPluginDispatchsRequestedControllerWhenFound()
    {
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
        $routeMatch  = $this->controller->getEvent()->getRouteMatch();
        $matchParams = $routeMatch->getParams();
        $result = $this->plugin->dispatch('forward', array(
            'action' => 'test-matches',
            'param1' => 'foobar',
        ));
        $test       = $this->controller->getEvent()->getRouteMatch();
        $testParams = $test->getParams();

        $this->assertSame($routeMatch, $test);
        $this->assertEquals($matchParams, $testParams);
    }
}
