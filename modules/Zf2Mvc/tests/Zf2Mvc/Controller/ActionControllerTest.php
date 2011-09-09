<?php

namespace Zf2Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\StaticEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zf2Mvc\Router\RouteMatch;

class ActionControllerTest extends TestCase
{
    public function setUp()
    {
        $this->controller = new TestAsset\SampleController();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample'));
        $this->request    = new Request();
        $this->request->setMetadata('route-match', $this->routeMatch);

        StaticEventManager::resetInstance();
    }

    public function testDispatchInvokesIndexActionWhenNoActionPresentInRouteMatch()
    {
        $result = $this->controller->dispatch($this->request);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Placeholder page', $result['content']);
    }

    public function testDispatchInvokesNotFoundActionWhenInvalidActionPresentInRouteMatch()
    {
        $this->routeMatch->setParam('action', 'totally-made-up-action');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Page not found', $result['content']);
    }

    public function testDispatchInvokesProvidedActionWhenMethodExists()
    {
        $this->routeMatch->setParam('action', 'test');
        $result = $this->controller->dispatch($this->request);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('test', $result['content']);
    }

    public function testDispatchCallsActionMethodBasedOnNormalizingAction()
    {
        $this->routeMatch->setParam('action', 'test.some-strangely_separated.words');
        $result = $this->controller->dispatch($this->request);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Test Some Strangely Separated Words', $result['content']);
    }

    public function testShortCircuitsBeforeActionIfPreDispatchReturnsAResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->events()->attach('dispatch.pre', function($e) use ($response) {
            return $response;
        });
        $result = $this->controller->dispatch($this->request);
        $this->assertSame($response, $result);
    }

    public function testPostDispatchEventAllowsReplacingResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->events()->attach('dispatch.post', function($e) use ($response) {
            return $response;
        });
        $result = $this->controller->dispatch($this->request);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnDispatchableInterfaceByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch.pre', function($e) use ($response) {
            return $response;
        });
        $result = $this->controller->dispatch($this->request);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnActionControllerClassByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach('Zf2Mvc\Controller\ActionController', 'dispatch.pre', function($e) use ($response) {
            return $response;
        });
        $result = $this->controller->dispatch($this->request);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnClassNameByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach(get_class($this->controller), 'dispatch.pre', function($e) use ($response) {
            return $response;
        });
        $result = $this->controller->dispatch($this->request);
        $this->assertSame($response, $result);
    }
}
