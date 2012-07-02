<?php

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\EventManager\SharedEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;

class RestfulControllerTest extends TestCase
{
    public $controller;
    public $request;
    public $response;
    public $routeMatch;
    public $event;

    public function setUp()
    {
        $this->controller = new TestAsset\RestfulTestController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-restful'));
        $this->event      = new MvcEvent;
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
    }

    public function testDispatchInvokesListWhenNoActionPresentAndNoIdentifierOnGet()
    {
        $entities = array(
            new stdClass,
            new stdClass,
            new stdClass,
        );
        $this->controller->entities = $entities;
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('entities', $result);
        $this->assertEquals($entities, $result['entities']);
        $this->assertEquals('getList', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesGetMethodWhenNoActionPresentAndIdentifierPresentOnGet()
    {
        $entity = new stdClass;
        $this->controller->entity = $entity;
        $this->routeMatch->setParam('id', 1);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('entity', $result);
        $this->assertEquals($entity, $result['entity']);
        $this->assertEquals('get', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesCreateMethodWhenNoActionPresentAndPostInvoked()
    {
        $entity = array('id' => 1, 'name' => __FUNCTION__);
        $this->request->setMethod('POST');
        $post = $this->request->getPost();
        $post->fromArray($entity);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('entity', $result);
        $this->assertEquals($entity, $result['entity']);
        $this->assertEquals('create', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesUpdateMethodWhenNoActionPresentAndPutInvokedWithIdentifier()
    {
        $entity = array('name' => __FUNCTION__);
        $string = http_build_query($entity);
        $this->request->setMethod('PUT')
                      ->setContent($string);
        $this->routeMatch->setParam('id', 1);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('entity', $result);
        $test = $result['entity'];
        $this->assertArrayHasKey('id', $test);
        $this->assertEquals(1, $test['id']);
        $this->assertArrayHasKey('name', $test);
        $this->assertEquals(__FUNCTION__, $test['name']);
        $this->assertEquals('update', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesDeleteMethodWhenNoActionPresentAndDeleteInvokedWithIdentifier()
    {
        $entity = array('id' => 1, 'name' => __FUNCTION__);
        $this->controller->entity = $entity;
        $this->request->setMethod('DELETE');
        $this->routeMatch->setParam('id', 1);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertEquals(array(), $result);
        $this->assertEquals(array(), $this->controller->entity);
        $this->assertEquals('delete', $this->routeMatch->getParam('action'));
    }

    public function testDispatchCallsActionMethodBasedOnNormalizingAction()
    {
        $this->routeMatch->setParam('action', 'test.some-strangely_separated.words');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('content', $result);
        $this->assertContains('Test Some Strangely Separated Words', $result['content']);
    }

    public function testDispatchCallsNotFoundActionWhenActionPassedThatCannotBeMatched()
    {
        $this->routeMatch->setParam('action', 'test-some-made-up-action');
        $result   = $this->controller->dispatch($this->request, $this->response);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('content', $result);
        $this->assertContains('Page not found', $result['content']);
    }

    public function testShortCircuitsBeforeActionIfPreDispatchReturnsAResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function($e) use ($response) {
            return $response;
        }, 10);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testPostDispatchEventAllowsReplacingResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function($e) use ($response) {
            return $response;
        }, -10);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnDispatchableInterfaceByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = new SharedEventManager();
        $events->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, function($e) use ($response) {
            return $response;
        }, 10);
        $this->controller->getEventManager()->setSharedManager($events);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnRestfulControllerClassByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = new SharedEventManager();
        $events->attach('Zend\Mvc\Controller\AbstractRestfulController', MvcEvent::EVENT_DISPATCH, function($e) use ($response) {
            return $response;
        }, 10);
        $this->controller->getEventManager()->setSharedManager($events);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testEventManagerListensOnClassNameByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = new SharedEventManager();
        $events->attach(get_class($this->controller), MvcEvent::EVENT_DISPATCH, function($e) use ($response) {
            return $response;
        }, 10);
        $this->controller->getEventManager()->setSharedManager($events);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testDispatchInjectsEventIntoController()
    {
        $this->controller->dispatch($this->request, $this->response);
        $event = $this->controller->getEvent();
        $this->assertNotNull($event);
        $this->assertSame($this->event, $event);
    }

    public function testControllerIsLocatorAware()
    {
        $this->assertInstanceOf('Zend\ServiceManager\ServiceLocatorAwareInterface', $this->controller);
    }

    public function testControllerIsEventAware()
    {
        $this->assertInstanceOf('Zend\Mvc\InjectApplicationEventInterface', $this->controller);
    }

    public function testControllerIsPluggable()
    {
        $this->assertTrue(method_exists($this->controller, 'plugin'));
    }

    public function testMethodOverloadingShouldReturnPluginWhenFound()
    {
        $plugin = $this->controller->url();
        $this->assertInstanceOf('Zend\Mvc\Controller\Plugin\Url', $plugin);
    }

    public function testMethodOverloadingShouldInvokePluginAsFunctorIfPossible()
    {
        $model = $this->event->getViewModel();
        $this->controller->layout('alternate/layout');
        $this->assertEquals('alternate/layout', $model->getTemplate());
    }
}
