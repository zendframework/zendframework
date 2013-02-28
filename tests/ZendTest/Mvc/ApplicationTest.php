<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionObject;
use stdClass;
use Zend\Config\Config;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class ApplicationTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        $appConfig = array(
            'modules' => array(),
            'module_listener_options' => array(
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => array(),
            ),
        );
        $config = function ($s) {
            return new Config(array(
                /*
                'controller' => array(
                    'classes' => array(
                        'bad'    => 'ZendTest\Mvc\Controller\TestAsset\BadController',
                        'path'   => 'ZendTest\Mvc\TestAsset\PathController',
                        'sample' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
                    ),
                ),
                 */
            ));
        };
        $sm = $this->serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'invokables' => array(
                    'DispatchListener' => 'Zend\Mvc\DispatchListener',
                    'Request'          => 'Zend\Http\PhpEnvironment\Request',
                    'Response'         => 'Zend\Http\PhpEnvironment\Response',
                    'RouteListener'    => 'Zend\Mvc\RouteListener',
                    'ViewManager'      => 'ZendTest\Mvc\TestAsset\MockViewManager',
                    'SendResponseListener' => 'ZendTest\Mvc\TestAsset\MockSendResponseListener'
                ),
                'factories' => array(
                    'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
                    'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
                    'RoutePluginManager'      => 'Zend\Mvc\Service\RoutePluginManagerFactory',
                    'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
                    'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
                    'Config'                  => $config,
                ),
                'aliases' => array(
                    'Router'                 => 'HttpRouter',
                    'Configuration'          => 'Config',
                ),
            ))
        );
        $sm->setService('ApplicationConfig', $appConfig);
        $sm->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');
        $sm->setAllowOverride(true);

        $this->application = $sm->get('Application');
    }

    public function getConfigListener()
    {
        $manager   = $this->serviceManager->get('ModuleManager');
        $listeners = $manager->getEventManager()->getListeners('loadModule');
        foreach ($listeners as $listener) {
            $callback = $listener->getCallback();
            if (!is_array($callback)) {
                continue;
            }
            $object = array_shift($callback);
            if (!$object instanceof \Zend\ModuleManager\Listener\ConfigListener) {
                continue;
            }
            return $object;
        }
    }

    public function testRequestIsPopulatedFromServiceManager()
    {
        $request = $this->serviceManager->get('Request');
        $this->assertSame($request, $this->application->getRequest());
    }

    public function testResponseIsPopulatedFromServiceManager()
    {
        $response = $this->serviceManager->get('Response');
        $this->assertSame($response, $this->application->getResponse());
    }

    public function testEventManagerIsPopulated()
    {
        $events       = $this->serviceManager->get('EventManager');
        $sharedEvents = $events->getSharedManager();
        $appEvents    = $this->application->getEventManager();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $appEvents);
        $this->assertNotSame($events, $appEvents);
        $this->assertSame($sharedEvents, $appEvents->getSharedManager());
    }

    public function testEventManagerListensOnApplicationContext()
    {
        $events      = $this->application->getEventManager();
        $identifiers = $events->getIdentifiers();
        $expected    = array('Zend\Mvc\Application');
        $this->assertEquals($expected, array_values($identifiers));
    }

    public function testServiceManagerIsPopulated()
    {
        $this->assertSame($this->serviceManager, $this->application->getServiceManager());
    }

    public function testConfigIsPopulated()
    {
        $smConfig  = $this->serviceManager->get('Config');
        $appConfig = $this->application->getConfig();
        $this->assertEquals($smConfig, $appConfig, sprintf('SM config: %s; App config: %s', var_export($smConfig, 1), var_export($appConfig, 1)));
    }

    public function testEventsAreEmptyAtFirst()
    {
        $events = $this->application->getEventManager();
        $registeredEvents = $events->getEvents();
        $this->assertEquals(array(), $registeredEvents);

        $sharedEvents = $events->getSharedManager();
        $this->assertAttributeEquals(array(), 'identifiers', $sharedEvents);
    }

    public function testBootstrapRegistersRouteListener()
    {
        $routeListener = $this->serviceManager->get('RouteListener');
        $this->application->bootstrap();
        $events = $this->application->getEventManager();
        $listeners = $events->getListeners(MvcEvent::EVENT_ROUTE);
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($routeListener, 'onRoute'), $callback);
    }

    public function testBootstrapRegistersDispatchListener()
    {
        $dispatchListener = $this->serviceManager->get('DispatchListener');
        $this->application->bootstrap();
        $events = $this->application->getEventManager();
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($dispatchListener, 'onDispatch'), $callback);
    }

    public function testBootstrapRegistersSendResponseListener()
    {
        $sendResponseListener = $this->serviceManager->get('SendResponseListener');
        $this->application->bootstrap();
        $events = $this->application->getEventManager();
        $listeners = $events->getListeners(MvcEvent::EVENT_FINISH);
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($sendResponseListener, 'sendResponse'), $callback);
    }

    public function testBootstrapRegistersViewManagerAsBootstrapListener()
    {
        $viewManager = $this->serviceManager->get('ViewManager');
        $this->application->bootstrap();
        $events = $this->application->getEventManager();
        $listeners = $events->getListeners(MvcEvent::EVENT_BOOTSTRAP);
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($viewManager, 'onBootstrap'), $callback);
    }

    public function testBootstrapRegistersConfiguredMvcEvent()
    {
        $this->assertNull($this->application->getMvcEvent());
        $this->application->bootstrap();
        $event = $this->application->getMvcEvent();
        $this->assertInstanceOf('Zend\Mvc\MvcEvent', $event);

        $request  = $this->application->getRequest();
        $response = $this->application->getResponse();
        $router   = $this->serviceManager->get('HttpRouter');

        $this->assertFalse($event->isError());
        $this->assertSame($request, $event->getRequest());
        $this->assertSame($response, $event->getResponse());
        $this->assertSame($router, $event->getRouter());
        $this->assertSame($this->application, $event->getApplication());
        $this->assertSame($this->application, $event->getTarget());
    }

    public function setupPathController($addService = true)
    {
        $request = $this->serviceManager->get('Request');
        $uri     = UriFactory::factory('http://example.local/path');
        $request->setUri($uri);

        $router = $this->serviceManager->get('HttpRouter');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/path',
            'defaults' => array(
                'controller' => 'path',
            ),
        ));
        $router->addRoute('path', $route);
        if ($addService) {
            $controllerLoader = $this->serviceManager->get('ControllerLoader');
            $controllerLoader->setFactory('path', function () {
                return new TestAsset\PathController;
            });
        }
        $this->application->bootstrap();
    }

    public function setupActionController()
    {
        $request = $this->serviceManager->get('Request');
        $uri     = UriFactory::factory('http://example.local/sample');
        $request->setUri($uri);

        $router = $this->serviceManager->get('HttpRouter');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/sample',
            'defaults' => array(
                'controller' => 'sample',
                'action'     => 'test',
            ),
        ));
        $router->addRoute('sample', $route);

        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->setFactory('sample', function () {
            return new Controller\TestAsset\SampleController;
        });
        $this->application->bootstrap();
    }

    public function setupBadController($addService = true)
    {
        $request = $this->serviceManager->get('Request');
        $uri     = UriFactory::factory('http://example.local/bad');
        $request->setUri($uri);

        $router = $this->serviceManager->get('HttpRouter');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/bad',
            'defaults' => array(
                'controller' => 'bad',
                'action'     => 'test',
            ),
        ));
        $router->addRoute('bad', $route);

        if ($addService) {
            $controllerLoader = $this->serviceManager->get('ControllerLoader');
            $controllerLoader->setFactory('bad', function () {
                return new Controller\TestAsset\BadController;
            });
        }
        $this->application->bootstrap();
    }

    public function testRoutingIsExecutedDuringRun()
    {
        $this->setupPathController();

        $log = array();
        $this->application->getEventManager()->attach(MvcEvent::EVENT_ROUTE, function ($e) use (&$log) {
            $match = $e->getRouteMatch();
            if (!$match) {
                return;
            }
            $log['route-match'] = $match;
        });

        $this->application->run();
        $this->assertArrayHasKey('route-match', $log);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $log['route-match']);
    }

    public function testAllowsReturningEarlyFromRouting()
    {
        $this->setupPathController();
        $response = new Response();

        $this->application->getEventManager()->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($response) {
            return $response;
        });

        $result = $this->application->run();
        $this->assertSame($response, $result);
    }

    public function testControllerIsDispatchedDuringRun()
    {
        $this->setupPathController();

        $response = $this->application->run();
        $this->assertContains('PathController', $response->getContent());
        $this->assertContains('dispatch', $response->toString());
    }

    /**
     * @group zen-92
     */
    public function testDispatchingInjectsLocatorInLocatorAwareControllers()
    {
        $this->setupActionController();

        $events  = $this->application->getEventManager()->getSharedManager();
        $storage = new ArrayObject();
        $events->attach('ZendTest\Mvc\Controller\TestAsset\SampleController', MvcEvent::EVENT_DISPATCH, function ($e) use ($storage) {
            $controller = $e->getTarget();
            $storage['locator'] = $controller->getServiceLocator();
            return $e->getResponse();
        }, 100);

        $this->application->run();

        $this->assertTrue(isset($storage['locator']));
        $this->assertSame($this->serviceManager, $storage['locator']);
    }

    public function testFinishEventIsTriggeredAfterDispatching()
    {
        $this->setupActionController();
        $this->application->getEventManager()->attach(MvcEvent::EVENT_FINISH, function ($e) {
            return $e->getResponse()->setContent($e->getResponse()->getBody() . 'foobar');
        });
        $this->application->run();
        $this->assertContains('foobar', $this->application->getResponse()->getBody(), 'The "finish" event was not triggered ("foobar" not in response)');
    }

    /**
     * @group error-handling
     */
    public function testExceptionsRaisedInDispatchableShouldRaiseDispatchErrorEvent()
    {
        $this->setupBadController();
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $exception = $e->getParam('exception');
            $response->setContent($exception->getMessage());
            return $response;
        });

        $this->application->run();
        $this->assertContains('Raised an exception', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testInabilityToRetrieveControllerShouldTriggerExceptionError()
    {
        $this->setupBadController(false);
        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller);
            return $response;
        });

        $this->application->run();
        $this->assertContains(Application::ERROR_CONTROLLER_NOT_FOUND, $response->getContent());
        $this->assertContains('bad', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testInabilityToRetrieveControllerShouldTriggerDispatchError()
    {
        $this->setupBadController(false);
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller);
            return $response;
        });

        $this->application->run();
        $this->assertContains(Application::ERROR_CONTROLLER_NOT_FOUND, $response->getContent());
        $this->assertContains('bad', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testInvalidControllerTypeShouldTriggerDispatchError()
    {
        $this->serviceManager->get('ControllerLoader');
        $this->setupBadController(false);
        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->setFactory('bad', function () {
            return new stdClass;
        });
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $class      = $e->getControllerClass();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller . '; Class: ' . $class);
            return $response;
        });

        $this->application->run();
        $this->assertContains(Application::ERROR_CONTROLLER_INVALID, $response->getContent());
        $this->assertContains('bad', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testRoutingFailureShouldTriggerDispatchError()
    {
        $this->setupBadController();
        $router = new Router\SimpleRouteStack();
        $event = $this->application->getMvcEvent();
        $event->setRouter($router);

        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $this->application->run();
        $this->assertTrue($event->isError());
        $this->assertContains(Application::ERROR_ROUTER_NO_MATCH, $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testLocatorExceptionShouldTriggerDispatchError()
    {
        $this->setupPathController(false);
        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $response = new Response();
        $this->application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            return $response;
        });

        $result = $this->application->run();
        $this->assertSame($response, $result, var_export($result, 1));
    }

    /**
     * @group error-handling
     */
    public function testFailureForRouteToReturnRouteMatchShouldPopulateEventError()
    {
        $this->setupBadController();
        $router = new Router\SimpleRouteStack();
        $event = $this->application->getMvcEvent();
        $event->setRouter($router);

        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $this->application->run();
        $this->assertTrue($event->isError());
        $this->assertEquals(Application::ERROR_ROUTER_NO_MATCH, $event->getError());
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfRouteEventReturnsResponse()
    {
        $this->application->bootstrap();
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach(MvcEvent::EVENT_FINISH, function ($e) use ($token) {
            $token->foo = 'bar';
        });

        $this->application->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar', $token->foo);
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfDispatchEventReturnsResponse()
    {
        $this->application->bootstrap();
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->clearListeners(MvcEvent::EVENT_ROUTE);
        $events->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach(MvcEvent::EVENT_FINISH, function ($e) use ($token) {
            $token->foo = 'bar';
        });

        $this->application->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar', $token->foo);
    }

    public function testApplicationShouldBeEventTargetAtFinishEvent()
    {
        $this->setupActionController();

        $events   = $this->application->getEventManager();
        $response = $this->application->getResponse();
        $events->attach(MvcEvent::EVENT_FINISH, function ($e) use ($response) {
            $response->setContent("EventClass: " . get_class($e->getTarget()));
            return $response;
        });

        $this->application->run();
        $this->assertContains('Zend\Mvc\Application', $response->getContent());
    }

    public function testOnDispatchErrorEventPassedToTriggersShouldBeTheOriginalOne()
    {
        $this->setupPathController(false);
        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $model = $this->getMock('Zend\View\Model\ViewModel');
        $this->application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use ($model) {
            $e->setResult($model);
        });

        $this->application->run();
        $event = $this->application->getMvcEvent();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $event->getResult());
    }

    /**
     * @group 2981
     */
    public function testReturnsResponseFromListenerWhenRouteEventShortCircuits()
    {
        $this->application->bootstrap();
        $testResponse = new Response();
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->clearListeners(MvcEvent::EVENT_DISPATCH);
        $events->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($testResponse) {
            $testResponse->setContent('triggered');
            return $testResponse;
        }, 100);

        $self      = $this;
        $triggered = false;
        $events->attach(MvcEvent::EVENT_FINISH, function ($e) use ($self, $testResponse, &$triggered) {
            $self->assertSame($testResponse, $e->getResponse());
            $triggered = true;
        });

        $this->application->run();
        $this->assertTrue($triggered);
    }

    /**
     * @group 2981
     */
    public function testReturnsResponseFromListenerWhenDispatchEventShortCircuits()
    {
        $this->application->bootstrap();
        $testResponse = new Response();
        $response = $this->application->getResponse();
        $events   = $this->application->getEventManager();
        $events->clearListeners(MvcEvent::EVENT_ROUTE);
        $events->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($testResponse) {
            $testResponse->setContent('triggered');
            return $testResponse;
        }, 100);

        $self      = $this;
        $triggered = false;
        $events->attach(MvcEvent::EVENT_FINISH, function ($e) use ($self, $testResponse, &$triggered) {
            $self->assertSame($testResponse, $e->getResponse());
            $triggered = true;
        });

        $this->application->run();
        $this->assertTrue($triggered);
    }

    public function testCompleteRequestShouldReturnApplicationInstance()
    {
        $r      = new ReflectionObject($this->application);
        $method = $r->getMethod('completeRequest');
        $method->setAccessible(true);

        $this->application->bootstrap();
        $event  = $this->application->getMvcEvent();
        $result = $method->invoke($this->application, $event);
        $this->assertSame($this->application, $result);
    }
}
