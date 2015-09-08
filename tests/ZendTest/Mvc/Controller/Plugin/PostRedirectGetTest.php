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
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\Segment as SegmentRoute;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\ModuleRouteListener;
use Zend\Stdlib\Parameters;
use ZendTest\Mvc\Controller\TestAsset\SampleController;
use ZendTest\Mvc\TestAsset\SessionManager;

class PostRedirectGetTest extends TestCase
{
    public $controller;
    public $event;
    public $request;
    public $response;

    public function setUp()
    {
        $router = new TreeRouteStack;
        $router->addRoute('home', LiteralRoute::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
            )
        )));

        $router->addRoute('sub', SegmentRoute::factory(array(
            'route' => '/foo/:param',
            'defaults' => array(
                'param' => 1
            )
        )));

        $router->addRoute('ctl', SegmentRoute::factory(array(
            'route' => '/ctl/:controller',
            'defaults' => array(
                '__NAMESPACE__' => 'ZendTest\Mvc\Controller\TestAsset',
                'controller' => 'sample'
            )
        )));

        $this->controller = new SampleController();
        $this->request    = new Request();
        $this->event      = new MvcEvent();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample', 'action' => 'postPage'));

        $this->event->setRequest($this->request);
        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRouter($router);

        $this->sessionManager = new SessionManager();
        $this->sessionManager->destroy();

        $this->controller->setEvent($this->event);
        $plugins = $this->controller->getPluginManager();
        $plugins->get('flashmessenger')->setSessionManager($this->sessionManager);
    }

    public function testReturnsFalseOnIntialGet()
    {
        $result    = $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->prg('home');

        $this->assertFalse($prgResult);
    }

    public function testRedirectsToUrlOnPost()
    {
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value'
        )));

        $result         = $this->controller->dispatch($this->request, $this->response);
        $prgResultUrl   = $this->controller->prg('/test/getPage', true);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultUrl);
        $this->assertTrue($prgResultUrl->getHeaders()->has('Location'));
        $this->assertEquals('/test/getPage', $prgResultUrl->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultUrl->getStatusCode());
    }

    public function testRedirectsToRouteOnPost()
    {
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $result         = $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg('home');

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testReturnsPostOnRedirectGet()
    {
        $params = array(
            'postval1' => 'value1'
        );
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters($params));

        $result         = $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg('home');

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());

        // Do GET
        $this->request = new Request();
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->prg('home');

        $this->assertEquals($params, $prgResult);

        // Do GET again to make sure data is empty
        $this->request = new Request();
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->prg('home');

        $this->assertFalse($prgResult);
    }

    /**
     * @expectedException Zend\Mvc\Exception\RuntimeException
     */
    public function testThrowsExceptionOnRouteWithoutRouter()
    {
        $controller = $this->controller;
        $controller = $controller->getEvent()->setRouter(new SimpleRouteStack);

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value'
        )));

        $result = $this->controller->dispatch($this->request, $this->response);
        $this->controller->prg('some/route');
    }

    public function testNullRouteUsesMatchedRouteName()
    {
        $this->controller->getEvent()->getRouteMatch()->setMatchedRouteName('home');

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $result         = $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg();

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testReuseMatchedParameters()
    {
        $this->controller->getEvent()->getRouteMatch()->setMatchedRouteName('sub');

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg();

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/foo/1', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testReuseMatchedParametersWithSegmentController()
    {
        $expects = '/ctl/sample';
        $this->request->setMethod('POST');
        $this->request->setUri($expects);
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $routeMatch = $this->event->getRouter()->match($this->request);
        $this->event->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener;
        $moduleRouteListener->onRoute($this->event);

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg();

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals($expects, $prgResultRoute->getHeaders()->get('Location')->getUri(), 'expects to redirect for the same url');
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testKeepUrlQueryParameters()
    {
        $expects = '/ctl/sample';
        $this->request->setMethod('POST');
        $this->request->setUri($expects);
        $this->request->setQuery(new Parameters(array(
            'id' => '123',
        )));

        $routeMatch = $this->event->getRouter()->match($this->request);
        $this->event->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener;
        $moduleRouteListener->onRoute($this->event);

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg();

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals($expects . '?id=123', $prgResultRoute->getHeaders()->get('Location')->getUri(), 'expects to redirect for the same url');
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }
}
