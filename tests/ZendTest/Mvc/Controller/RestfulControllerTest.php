<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\EventManager\SharedEventManager;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

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
        $this->request    = new TestAsset\Request();
        $this->response   = new Response();
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

    public function testDispatchInvokesReplaceListMethodWhenNoActionPresentAndPutInvokedWithoutIdentifier()
    {
        $entities = array(
            array('id' => uniqid(), 'name' => __FUNCTION__),
            array('id' => uniqid(), 'name' => __FUNCTION__),
            array('id' => uniqid(), 'name' => __FUNCTION__),
        );
        $string = http_build_query($entities);
        $this->request->setMethod('PUT')
                      ->setContent($string);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertEquals($entities, $result);
        $this->assertEquals('replaceList', $this->routeMatch->getParam('action'));
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

    public function testDispatchInvokesDeleteListMethodWhenNoActionPresentAndDeleteInvokedWithoutIdentifier()
    {
        $this->request->setMethod('DELETE');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($this->response, $result);
        $this->assertEquals(204, $result->getStatusCode());
        $this->assertTrue($result->getHeaders()->has('X-Deleted'));
        $this->assertEquals('deleteList', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesOptionsMethodWhenNoActionPresentAndOptionsInvoked()
    {
        $this->request->setMethod('OPTIONS');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($this->response, $result);
        $this->assertEquals('options', $this->routeMatch->getParam('action'));
        $headers = $result->getHeaders();
        $this->assertTrue($headers->has('Allow'));
        $allow = $headers->get('Allow');
        $expected = explode(', ', 'GET, POST, PUT, DELETE, PATCH, HEAD, TRACE');
        sort($expected);
        $test     = explode(', ', $allow->getFieldValue());
        sort($test);
        $this->assertEquals($expected, $test);
    }

    public function testDispatchInvokesPatchMethodWhenNoActionPresentAndPatchInvokedWithIdentifier()
    {
        $entity = new stdClass;
        $entity->name = 'foo';
        $entity->type = 'standard';
        $this->controller->entity = $entity;
        $entity = array('name' => __FUNCTION__);
        $string = http_build_query($entity);
        $this->request->setMethod('PATCH')
                      ->setContent($string);
        $this->routeMatch->setParam('id', 1);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('entity', $result);
        $test = $result['entity'];
        $this->assertArrayHasKey('id', $test);
        $this->assertEquals(1, $test['id']);
        $this->assertArrayHasKey('name', $test);
        $this->assertEquals(__FUNCTION__, $test['name']);
        $this->assertArrayHasKey('type', $test);
        $this->assertEquals('standard', $test['type']);
        $this->assertEquals('patch', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesHeadMethodWhenNoActionPresentAndHeadInvokedWithoutIdentifier()
    {
        $entities = array(
            new stdClass,
            new stdClass,
            new stdClass,
        );
        $this->controller->entities = $entities;
        $this->request->setMethod('HEAD');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($this->response, $result);
        $content = $result->getContent();
        $this->assertEquals('', $content);
        $this->assertEquals('head', $this->routeMatch->getParam('action'));
    }

    public function testDispatchInvokesHeadMethodWhenNoActionPresentAndHeadInvokedWithIdentifier()
    {
        $entity = new stdClass;
        $this->controller->entity = $entity;
        $this->routeMatch->setParam('id', 1);
        $this->request->setMethod('HEAD');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($this->response, $result);
        $content = $result->getContent();
        $this->assertEquals('', $content);
        $this->assertEquals('head', $this->routeMatch->getParam('action'));

        $headers = $this->controller->getResponse()->getHeaders();
        $this->assertTrue($headers->has('X-ZF2-Id'));
        $header  = $headers->get('X-ZF2-Id');
        $this->assertEquals(1, $header->getFieldValue());
    }

    public function testAllowsRegisteringCustomHttpMethodsWithHandlers()
    {
        $this->controller->addHttpMethodHandler('DESCRIBE', array($this->controller, 'describe'));
        $this->request->setMethod('DESCRIBE');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertArrayHasKey('description', $result);
        $this->assertContains('::describe', $result['description']);
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
        $this->controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
            return $response;
        }, 10);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertSame($response, $result);
    }

    public function testPostDispatchEventAllowsReplacingResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
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
        $events->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
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
        $events->attach('Zend\Mvc\Controller\AbstractRestfulController', MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
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
        $events->attach(get_class($this->controller), MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
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

    public function testParsingDataAsJsonWillReturnAsArray()
    {
        $this->request->setMethod('POST');
        $this->request->getHeaders()->addHeaderLine('Content-type', 'application/json');
        $this->request->setContent('{"foo":"bar"}');
        $this->controller->getEventManager()->setSharedManager(new SharedEventManager());

        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertInternalType('array', $result);
        $this->assertEquals(array('entity' => array('foo' => 'bar')), $result);
    }

    public function matchingContentTypes()
    {
        return array(
            'exact-first' => array('application/hal+json'),
            'exact-second' => array('application/json'),
            'with-charset' => array('application/json; charset=utf-8'),
            'with-whitespace' => array('application/json '),
        );
    }

    /**
     * @dataProvider matchingContentTypes
     */
    public function testRequestingContentTypeReturnsTrueForValidMatches($contentType)
    {
        $this->request->getHeaders()->addHeaderLine('Content-Type', $contentType);
        $this->assertTrue($this->controller->requestHasContentType($this->request, TestAsset\RestfulTestController::CONTENT_TYPE_JSON));
    }

    public function nonMatchingContentTypes()
    {
        return array(
            'specific-type' => array('application/xml'),
            'generic-type' => array('text/json'),
        );
    }

    /**
     * @dataProvider nonMatchingContentTypes
     */
    public function testRequestingContentTypeReturnsFalseForInvalidMatches($contentType)
    {
        $this->request->getHeaders()->addHeaderLine('Content-Type', $contentType);
        $this->assertFalse($this->controller->requestHasContentType($this->request, TestAsset\RestfulTestController::CONTENT_TYPE_JSON));
    }

    public function testDispatchViaPatchWithoutIdentifierReturns405Response()
    {
        $entity = new stdClass;
        $entity->name = 'foo';
        $entity->type = 'standard';
        $this->controller->entity = $entity;
        $entity = array('name' => __FUNCTION__);
        $string = http_build_query($entity);
        $this->request->setMethod('PATCH')
                      ->setContent($string);
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(405, $result->getStatusCode());
    }

    public function testDispatchWithUnrecognizedMethodReturns405Response()
    {
        $this->request->setMethod('PROPFIND');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(405, $result->getStatusCode());
    }
}
