<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Config\Config;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Modulemanager\ModuleManager;
use Zend\Mvc\Application;
use Zend\Mvc\Router;
use Zend\Mvc\Service\ServiceManagerConfiguration;
use Zend\Mvc\View\ViewManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ApplicationTest extends TestCase
{
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
        $config = function($s) {
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
            new ServiceManagerConfiguration(array(
                'services'  => array(
                    'ViewManager' => 'ZendTest\Mvc\TestAsset\MockViewManager'
                ),
                'factories' => array(
                    'Configuration' => $config,
                ),
            ))
        );
        $sm->setService('ApplicationConfiguration', $appConfig);
        $sm->setAllowOverride(true);

        $this->application = $sm->get('Application');
    }

    public function getConfigListener()
    {
        $manager   = $this->serviceManager->get('ModuleManager');
        $listeners = $manager->events()->getListeners('loadModule');
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

    public function testModuleManagerIsPopulatedFromServiceManager()
    {
        $modules = $this->serviceManager->get('ModuleManager');
        $this->assertObjectHasAttribute('moduleManager', $this->application);
        $this->assertAttributeSame($modules, 'moduleManager', $this->application);
    }

    public function testEventManagerIsPopulated()
    {
        $events       = $this->serviceManager->get('EventManager');
        $sharedEvents = $events->getSharedManager();
        $appEvents    = $this->application->events();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $appEvents);
        $this->assertNotSame($events, $appEvents);
        $this->assertSame($sharedEvents, $appEvents->getSharedManager());
    }

    public function testEventManagerListensOnApplicationContext()
    {
        $events      = $this->application->events();
        $identifiers = $events->getIdentifiers();
        $expected    = array('Zend\Mvc\Application', 'application');
        $this->assertEquals($expected, array_values($identifiers));
    }

    public function testServiceManagerIsPopulated()
    {
        $this->assertSame($this->serviceManager, $this->application->getServiceManager());
    }

    public function testConfigurationIsPopulated()
    {
        $smConfig  = $this->serviceManager->get('Configuration');
        $appConfig = $this->application->getConfiguration();
        $this->assertEquals($smConfig, $appConfig, sprintf('SM config: %s; App config: %s', var_export($smConfig, 1), var_export($appConfig, 1)));
    }

    public function testEventsAreEmptyAtFirst()
    {
        $events = $this->application->events();
        $registeredEvents = $events->getEvents();
        $this->assertEquals(array(), $registeredEvents);

        $sharedEvents = $events->getSharedManager();
        $this->assertAttributeEquals(array(), 'identifiers', $sharedEvents);
    }

    public function testBootstrapRegistersRouteListener()
    {
        $routeListener = $this->serviceManager->get('RouteListener');
        $this->application->bootstrap();
        $events = $this->application->events();
        $listeners = $events->getListeners('route');
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($routeListener, 'onRoute'), $callback);
    }

    public function testBootstrapRegistersDispatchListener()
    {
        $dispatchListener = $this->serviceManager->get('DispatchListener');
        $this->application->bootstrap();
        $events = $this->application->events();
        $listeners = $events->getListeners('dispatch');
        $this->assertEquals(1, count($listeners));
        $listener = $listeners->top();
        $callback = $listener->getCallback();
        $this->assertSame(array($dispatchListener, 'onDispatch'), $callback);
    }

    public function testBootstrapRegistersViewManagerAsBootstrapListener()
    {
        $viewManager = $this->serviceManager->get('ViewManager');
        $this->application->bootstrap();
        $events = $this->application->events();
        $listeners = $events->getListeners('bootstrap');
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
        $router   = $this->serviceManager->get('Router');

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

        $router = $this->serviceManager->get('Router');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/path',
            'defaults' => array(
                'controller' => 'path',
            ),
        ));
        $router->addRoute('path', $route);

        if ($addService) {
            $controllerLoader = $this->serviceManager->get('ControllerLoader');
            $controllerLoader->setFactory('path', function() {
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

        $router = $this->serviceManager->get('Router');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/sample',
            'defaults' => array(
                'controller' => 'sample',
                'action'     => 'test',
            ),
        ));
        $router->addRoute('sample', $route);

        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->setFactory('sample', function() {
            return new Controller\TestAsset\SampleController;
        });
        $this->application->bootstrap();
    }

    public function setupBadController($addService = true)
    {
        $request = $this->serviceManager->get('Request');
        $uri     = UriFactory::factory('http://example.local/bad');
        $request->setUri($uri);

        $router = $this->serviceManager->get('Router');
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
            $controllerLoader->setFactory('bad', function() {
                return new Controller\TestAsset\BadController;
            });
        }
        $this->application->bootstrap();
    }

    public function testRoutingIsExecutedDuringRun()
    {
        $this->setupPathController();

        $log = array();
        $this->application->events()->attach('route', function($e) use (&$log) {
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

        $this->application->events()->attach('route', function($e) use ($response) {
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

    public function testDispatchingInjectsLocatorInLocatorAwareControllers()
    {
        $this->setupActionController();

        $events  = $this->application->events()->getSharedManager();
        $storage = new ArrayObject();
        $events->attach('ZendTest\Mvc\Controller\TestAsset\SampleController', 'dispatch', function ($e) use ($storage) {
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
        $this->application->events()->attach('finish', function($e) {
            return $e->getResponse()->setContent($e->getResponse()->getBody() . 'foobar');
        });
        $this->assertContains('foobar', $this->application->run()->getBody(), 'The "finish" event was not triggered ("foobar" not in response)');
    }

    /**
     * @group error-handling
     */
    public function testExceptionsRaisedInDispatchableShouldRaiseDispatchErrorEvent()
    {
        $this->setupBadController();
        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
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
        $controllerLoader->setInvokableClass('bad', 'DoesNotExist');
        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
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
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
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
        $controllerLoader->setFactory('bad', function() {
            return new stdClass;
        });
        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $class      = $e->getControllerClass();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller . '; Class: ' . $class);
            return $response;
        });

        $this->application->run();
        $this->assertContains(Application::ERROR_CONTROLLER_INVALID, $response->getContent());
        $this->assertContains('bad', $response->getContent());
        $this->assertContains('stdClass', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testRoutingFailureShouldTriggerDispatchError()
    {
        $this->setupBadController();
        $router = new Router\SimpleRouteStack();
        $this->application->getMvcEvent()->setRouter($router);

        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $this->application->run();
        $this->assertContains(Application::ERROR_ROUTER_NO_MATCH, $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testLocatorExceptionShouldTriggerDispatchError()
    {
        $this->setupPathController(false);
        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->setInvokableClass('path', 'InvalidClassName');
        $response = new Response();
        $this->application->events()->attach('dispatch.error', function($e) use ($response) {
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
        $this->application->getMvcEvent()->setRouter($router);

        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $this->application->run();
        $event = $this->application->getMvcEvent();
        $this->assertEquals(Application::ERROR_ROUTER_NO_MATCH, $event->getError());
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfRouteEventReturnsResponse()
    {
        $this->application->bootstrap();
        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->attach('route', function($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach('finish', function($e) use ($token) {
            $token->foo = 'bar';
        });

        $this->application->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar',$token->foo);
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfDispatchEventReturnsResponse()
    {
        $this->application->bootstrap();
        $response = $this->application->getResponse();
        $events   = $this->application->events();
        $events->clearListeners('route');
        $events->attach('dispatch', function($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach('finish', function($e) use ($token) {
            $token->foo = 'bar';
        });

        $this->application->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar',$token->foo);
    }

    public function testApplicationShouldBeEventTargetAtFinishEvent()
    {
        $this->setupActionController();

        $events   = $this->application->events();
        $response = $this->application->getResponse();
        $events->attach('finish', function ($e) use ($response) {
            $response->setContent("EventClass: " . get_class($e->getTarget()));
            return $response;
        });

        $this->application->run();
        $this->assertContains('Zend\Mvc\Application', $response->getContent());
    }
}
