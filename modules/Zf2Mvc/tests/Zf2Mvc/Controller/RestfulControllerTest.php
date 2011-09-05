<?php

namespace Zf2Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\EventManager\StaticEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zf2Mvc\Router\RouteMatch;

class RestfulControllerTest extends TestCase
{
    public function setUp()
    {
        $this->controller = new TestAsset\RestfulController();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-restful'));
        $this->request    = new Request();
        $this->request->setMetadata('route-match', $this->routeMatch);

        StaticEventManager::resetInstance();
    }

    public function testDispatchInvokesListWhenNoActionPresentAndNoIdentifierOnGet()
    {
        $entities = array(
            new stdClass,
            new stdClass,
            new stdClass,
        );
        $this->controller->entities = $entities;
        $result = $this->controller->dispatch($this->request);
        $this->assertArrayHasKey('entities', $result);
        $this->assertEquals($entities, $result['entities']);
    }

    public function testDispatchInvokesGetMethodWhenNoActionPresentAndIdentifierPresentOnGet()
    {
        $entity = new stdClass;
        $this->controller->entity = $entity;
        $this->request->setMetadata('id', 1);
        $result = $this->controller->dispatch($this->request);
        $this->assertArrayHasKey('entity', $result);
        $this->assertEquals($entity, $result['entity']);
    }

    public function testDispatchInvokesCreateMethodWhenNoActionPresentAndPostInvoked()
    {
        $entity = array('id' => 1, 'name' => __FUNCTION__);
        $this->request->setMethod('POST');
        $post = $this->request->post();
        $post->fromArray($entity);
        $result = $this->controller->dispatch($this->request);
        $this->assertArrayHasKey('entity', $result);
        $this->assertEquals($entity, $result['entity']);
    }

    public function testDispatchInvokesUpdateMethodWhenNoActionPresentAndPutInvokedWithIdentifier()
    {
        $entity = array('name' => __FUNCTION__);
        $string = http_build_query($entity);
        $this->request->setMethod('PUT')
                      ->setContent($string)
                      ->setMetadata('id', 1);
        $result = $this->controller->dispatch($this->request);
        $this->assertArrayHasKey('entity', $result);
        $test = $result['entity'];
        $this->assertArrayHasKey('id', $test);
        $this->assertEquals(1, $test['id']);
        $this->assertArrayHasKey('name', $test);
        $this->assertEquals(__FUNCTION__, $test['name']);
    }

    public function testDispatchInvokesDeleteMethodWhenNoActionPresentAndDeleteInvokedWithIdentifier()
    {
        $entity = array('id' => 1, 'name' => __FUNCTION__);
        $this->controller->entity = $entity;
        $this->request->setMethod('DELETE')
                      ->setMetadata('id', 1);
        $result = $this->controller->dispatch($this->request);
        $this->assertEquals(array(), $result);
        $this->assertEquals(array(), $this->controller->entity);
    }

    public function testDispatchCallsActionMethodBasedOnNormalizingAction()
    {
        $this->routeMatch->setParam('action', 'test.some-strangely_separated.words');
        $result = $this->controller->dispatch($this->request);
        $this->assertArrayHasKey('content', $result);
        $this->assertContains('Test Some Strangely Separated Words', $result['content']);
    }

    public function testDispatchCallsNotFoundActionWhenActionPassedThatCannotBeMatched()
    {
        $this->routeMatch->setParam('action', 'test-some-made-up-action');
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('content', $result);
        $this->assertContains('Page not found', $result['content']);
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

    public function testEventManagerListensOnRestfulControllerClassByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = StaticEventManager::getInstance();
        $events->attach('Zf2Mvc\Controller\RestfulController', 'dispatch.pre', function($e) use ($response) {
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
