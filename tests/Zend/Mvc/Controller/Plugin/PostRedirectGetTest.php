<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Stdlib\Parameters;
use ZendTest\Mvc\Controller\TestAsset\SampleController;
use ZendTest\Session\TestAsset\TestManager as SessionManager;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 */
class PostRedirectGetTest extends TestCase
{
    public $controller;
    public $event;
    public $request;
    public $response;

    public function setUp()
    {
        $router = new SimpleRouteStack;
        $router->addRoute('home', LiteralRoute::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
            ),
        )));

        $this->controller = new SampleController();
        $this->request    = new Request();
        $this->event      = new MvcEvent();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample', 'action' => 'postPage'));

        $this->event->setRequest($this->request);
        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRouter($router);

        $this->sessionManager = new SessionManager();

        $this->controller->setEvent($this->event);
        $this->controller->flashMessenger()->setSessionManager($this->sessionManager);
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
    }

    public function testRedirectsToRouteOnPost()
    {
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value'
        )));

        $result         = $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->prg('home');

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
    }

    public function testReturnsFalseOnIntialGet()
    {
        $result    = $this->controller->dispatch($this->request, $this->response);
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
}
