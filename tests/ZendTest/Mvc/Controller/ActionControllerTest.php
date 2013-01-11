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
use Zend\Console\Response as ConsoleResponse;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\StaticEventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class ActionControllerTest extends TestCase
{
    public $controller;
    public $event;
    public $request;
    public $response;

    public function setUp()
    {
        StaticEventManager::resetInstance();
        $this->controller = new TestAsset\SampleController();
        $this->request    = new Request();
        $this->response   = null;
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
    }

    public function testDispatchInvokesNotFoundActionWhenNoActionPresentInRouteMatch()
    {
        $result = $this->controller->dispatch($this->request, $this->response);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertInstanceOf('Zend\View\Model\ModelInterface', $result);
        $this->assertEquals('content', $result->captureTo());
        $vars = $result->getVariables();
        $this->assertArrayHasKey('content', $vars, var_export($vars, 1));
        $this->assertContains('Page not found', $vars['content']);
    }

    public function testDispatchInvokesNotFoundActionWhenInvalidActionPresentInRouteMatch()
    {
        $this->routeMatch->setParam('action', 'totally-made-up-action');
        $result = $this->controller->dispatch($this->request, $this->response);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertInstanceOf('Zend\View\Model\ModelInterface', $result);
        $this->assertEquals('content', $result->captureTo());
        $vars = $result->getVariables();
        $this->assertArrayHasKey('content', $vars, var_export($vars, 1));
        $this->assertContains('Page not found', $vars['content']);
    }

    public function testDispatchInvokesProvidedActionWhenMethodExists()
    {
        $this->routeMatch->setParam('action', 'test');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('test', $result['content']);
    }

    public function testDispatchCallsActionMethodBasedOnNormalizingAction()
    {
        $this->routeMatch->setParam('action', 'test.some-strangely_separated.words');
        $result = $this->controller->dispatch($this->request, $this->response);
        $this->assertTrue(isset($result['content']));
        $this->assertContains('Test Some Strangely Separated Words', $result['content']);
    }

    public function testShortCircuitsBeforeActionIfPreDispatchReturnsAResponse()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $this->controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
            return $response;
        }, 100);
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

    public function testEventManagerListensOnActionControllerClassByDefault()
    {
        $response = new Response();
        $response->setContent('short circuited!');
        $events = new SharedEventManager();
        $events->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
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

    public function testComposesPluginManagerByDefault()
    {
        $plugins = $this->controller->getPluginManager();
        $this->assertInstanceOf('Zend\Mvc\Controller\PluginManager', $plugins);
    }

    public function testPluginManagerComposesController()
    {
        $plugins    = $this->controller->getPluginManager();
        $controller = $plugins->getController();
        $this->assertSame($this->controller, $controller);
    }

    public function testInjectingPluginManagerSetsControllerWhenPossible()
    {
        $plugins = new PluginManager();
        $this->assertNull($plugins->getController());
        $this->controller->setPluginManager($plugins);
        $this->assertSame($this->controller, $plugins->getController());
        $this->assertSame($plugins, $this->controller->getPluginManager());
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

    /**
     * @group 3186
     */
    public function testNotFoundActionReturnsSuccessfullyForConsoleResponse()
    {
        $response     = new ConsoleResponse();
        $result       = $this->controller->dispatch($this->request, $response);
        $testResponse = $this->controller->getResponse();
        $this->assertSame($response, $testResponse);
        $this->assertInstanceOf('Zend\View\Model\ConsoleModel', $result);
        $vars = $result->getVariables();
        $this->assertTrue(isset($vars['result']));
        $this->assertContains('Page not found', $vars['result']);
        $this->assertEquals(1, $result->getErrorLevel());
    }
}
