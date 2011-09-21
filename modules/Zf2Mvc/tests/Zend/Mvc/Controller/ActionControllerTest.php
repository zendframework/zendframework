<?php

namespace Zend\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\StaticEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;

class ActionControllerTest extends TestCase
{
    public $controller;
    public $event;
    public $request;
    public $response;

    public function setUp()
    {
        $this->controller = new TestAsset\SampleController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);

        StaticEventManager::resetInstance();
    }

    public function testDispatchInvokesIndexActionWhenNoActionPresentInRouteMatch()
    {
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Placeholder page', $result['content']);
    }

    public function testDispatchInvokesNotFoundActionWhenInvalidActionPresentInRouteMatch()
    {
        $this->routeMatch->setParam('action', 'totally-made-up-action');
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Page not found', $result['content']);
    }

    public function testDispatchInvokesProvidedActionWhenMethodExists()
    {
        $this->routeMatch->setParam('action', 'test');
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('test', $result['content']);
    }

    public function testDispatchCallsActionMethodBasedOnNormalizingAction()
    {
        $this->routeMatch->setParam('action', 'test.some-strangely_separated.words');
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Test Some Strangely Separated Words', $result['content']);
    }

    public function testShortCircuitsBeforeActionIfPreDispatchReturnsAResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->events()->attach('dispatch', function($e) use ($response) {
            return $response;
        }, 100);
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertSame($response, $result);
    }

    public function testPostDispatchEventAllowsReplacingResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->events()->attach('dispatch', function($e) use ($response) {
            return $response;
        }, -10);
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnDispatchableInterfaceByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', function($e) use ($response) {
            return $response;
        }, 10);
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnActionControllerClassByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Mvc\Controller\ActionController', 'dispatch', function($e) use ($response) {
            return $response;
        }, 10);
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnClassNameByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach(get_class($this->controller), 'dispatch', function($e) use ($response) {
            return $response;
        }, 10);
        $result = $this->controller->dispatch($this->request, $this->response, $this->event);
        $this->assertSame($response, $result);
    }
}
