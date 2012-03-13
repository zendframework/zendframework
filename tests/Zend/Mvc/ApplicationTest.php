<?php

namespace ZendTest\Mvc;

use ArrayObject,
    PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Di\Di as DependencyInjector,
    Zend\Di\ServiceLocator,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Http\Request,
    Zend\Http\PhpEnvironment\Response,
    Zend\Mvc\Application,
    Zend\Mvc\Router,
    Zend\Uri\UriFactory;

class ApplicationTest extends TestCase
{
    public function setUp()
    {
        StaticEventManager::resetInstance();
    }

    public function testEventManagerIsLazyLoaded()
    {
        $app = new Application();
        $events = $app->events();
        $this->assertInstanceOf('Zend\EventManager\EventCollection', $events);
        $this->assertInstanceOf('Zend\EventManager\EventManager', $events);
    }

    public function testLocatorIsNullByDefault()
    {
        $app = new Application();
        $this->assertNull($app->getLocator());
    }

    public static function validLocators()
    {
        return array(
            array(new ServiceLocator()),
            array(new DependencyInjector()),
            array(new TestAsset\Locator()),
        );
    }

    /**
     * @dataProvider validLocators
     */
    public function testCanRetrieveLocatorOnceSet($locator)
    {
        $app     = new Application();
        $app->setLocator($locator);
        $this->assertSame($locator, $app->getLocator());
    }

    public function testRouterIsLazyLoaded()
    {
        $app    = new Application();
        $router = $app->getRouter();
        $this->assertInstanceOf('Zend\Mvc\Router\RouteStack', $router);
    }

    public function testRouterMayBeInjected()
    {
        $app    = new Application();
        $router = new Router\SimpleRouteStack();
        $app->setRouter($router);
        $this->assertSame($router, $app->getRouter());
    }

    public function testRequestIsLazyLoaded()
    {
        $app     = new Application();
        $request = $app->getRequest();
        $this->assertInstanceOf('Zend\Http\Request', $request);
    }

    public function testRequestMayBeInjected()
    {
        $app     = new Application();
        $request = new Request();
        $app->setRequest($request);
        $this->assertSame($request, $app->getRequest());
    }

    public function testResponseIsLazyLoaded()
    {
        $app      = new Application();
        $response = $app->getResponse();
        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    public function testResponseMayBeInjected()
    {
        $app      = new Application();
        $response = new Response();
        $app->setResponse($response);
        $this->assertSame($response, $app->getResponse());
    }

    public function testRunRaisesAnExceptionIfNoLocatorIsAvailable()
    {
        $app = new Application();

        $request = new Request();
        $uri     = UriFactory::factory('http://example.local/path');
        $request->setUri($uri);
        $app->setRequest($request);

        $route = Router\Http\Literal::factory(array(
            'route'    => '/path',
            'defaults' => array(
                'controller' => 'path',
            ),
        ));
        $router  = $app->getRouter();
        $router->addRoute('path', $route);

        $this->setExpectedException('RuntimeException');
        $app->run();
    }

    public function setupPathController()
    {
        $app = new Application();

        $request = new Request();
        $uri     = UriFactory::factory('http://example.local/path');
        $request->setUri($uri);
        $app->setRequest($request);

        $route = Router\Http\Literal::factory(array(
            'route'    => '/path',
            'defaults' => array(
                'controller' => 'path',
            ),
        ));
        $router  = $app->getRouter();
        $router->addRoute('path', $route);

        $locator = new TestAsset\Locator();
        $locator->add('path', function() {
            return new TestAsset\PathController;
        });
        $app->setLocator($locator);


        return $app;
    }

    public function setupActionController()
    {
        $app = new Application();

        $request = new Request();
        $uri     = UriFactory::factory('http://example.local/sample');
        $request->setUri($uri);
        $app->setRequest($request);

        $route = Router\Http\Literal::factory(array(
            'route'    => '/sample',
            'defaults' => array(
                'controller' => 'sample',
                'action'     => 'test',
            ),
        ));
        $router  = $app->getRouter();
        $router->addRoute('sample', $route);

        $locator = new TestAsset\Locator();
        $locator->add('sample', function() {
            return new Controller\TestAsset\SampleController;
        });
        $app->setLocator($locator);

        return $app;
    }

    public function setupBadController()
    {
        $app = new Application();

        $request = new Request();
        $uri     = UriFactory::factory('http://example.local/bad');
        $request->setUri($uri);
        $app->setRequest($request);

        $route = Router\Http\Literal::factory(array(
            'route'    => '/bad',
            'defaults' => array(
                'controller' => 'bad',
                'action'     => 'test',
            ),
        ));
        $router  = $app->getRouter();
        $router->addRoute('bad', $route);

        $locator = new TestAsset\Locator();
        $locator->add('bad', function() {
            return new Controller\TestAsset\BadController;
        });
        $app->setLocator($locator);

        return $app;
    }

    public function testRoutingIsExecutedDuringRun()
    {
        $app = $this->setupPathController();

        $log = array();
        $app->events()->attach('route', function($e) use (&$log) {
            $match = $e->getRouteMatch();
            if (!$match) {
                return;
            }
            $log['route-match'] = $match;
        });

        $app->run();
        $this->assertArrayHasKey('route-match', $log);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $log['route-match']);
    }

    public function testAllowsReturningEarlyFromRouting()
    {
        $app = $this->setupPathController();
        $response = new Response();

        $app->events()->attach('route', function($e) use ($response) {
            return $response;
        });

        $result = $app->run();
        $this->assertSame($response, $result);
    }

    public function testControllerIsDispatchedDuringRun()
    {
        $app = $this->setupPathController();

        $response = $app->run();
        $this->assertContains('PathController', $response->getContent());
        $this->assertContains('dispatch', $response->toString());
    }

    public function testDefaultRequestObjectContainsPhpEnvironmentContainers()
    {
        $app = new Application();
        $request = $app->getRequest();
        $query = $request->query();
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $query);
        $post = $request->post();
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $post);
    }

    public function testDefaultRequestObjectMirrorsEnvSuperglobal()
    {
        if (empty($_ENV)) {
            $this->markTestSkipped('ENV is empty');
        }
        $app = new Application();
        $req = $app->getRequest();
        $env = $req->env();
        $this->assertSame($_ENV, $env->toArray());
    }

    public function testDefaultRequestObjectMirrorsServerSuperglobal()
    {
        $app    = new Application();
        $req    = $app->getRequest();
        $server = $req->server();
        $this->assertSame($_SERVER, $server->toArray());
    }

    public function testDefaultRequestObjectMirrorsCookieSuperglobal()
    {
        $_COOKIE = array('foo' => 'bar');
        $app     = new Application();
        $req     = $app->getRequest();
        $cookie  = $req->cookie();
        $this->assertInstanceOf('Zend\Http\Header\Cookie', $cookie);
        $this->assertSame($_COOKIE, $cookie->getArrayCopy());
    }

    public function testDefaultRequestObjectMirrorsFilesSuperglobal()
    {
        $_FILES  = array(
            'foo.txt' => array(
                'name'     => 'foo.txt',
                'type'     => 'text/plain',
                'size'     => 0,
                'tmp_name' => '/tmp/' . uniqid(),
                'error'    => 0,
            ),
        );
        $app     = new Application();
        $req     = $app->getRequest();
        $files   = $req->file();
        $this->assertSame($_FILES, $files->getArrayCopy());
    }

    public static function methods()
    {
        $methods = array(
            'OPTIONS',
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'TRACE',
            'CONNECT',
        );

        $values = array();
        foreach ($methods as $key => $method) {
            $maskMethods = $methods;
            unset($maskMethods[$key]);
            $values[] = array($method, $maskMethods);
        }
        return $values;
    }

    /**
     * @dataProvider methods
     */
    public function testDefaultRequestObjectRequestMethodMirrorsServerHttpMethodKey($method, $methods)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $app = new Application();
        $req = $app->getRequest();

        $testMethod = 'is' . ucfirst(strtolower($method));
        $this->assertTrue($req->$testMethod());

        foreach ($methods as $test) {
            $testMethod = 'is' . ucfirst($test);
            $this->assertFalse($req->$testMethod());
        }
    }

    public function testDefaultRequestObjectContainsUriCreatedFromServerRequestUri()
    {
        $_SERVER['HTTP_HOST'] = 'framework.zend.com';
        $_SERVER['REQUEST_URI'] = '/api/zf-version?test=this';
        $_SERVER['QUERY_STRING'] = 'test=this';

        $app = new Application();
        $req = $app->getRequest();

        $uri = $req->uri();
        $this->assertInstanceOf('Zend\Uri\Uri', $uri);
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('framework.zend.com', $uri->getHost());
        $this->assertEquals('/api/zf-version', $uri->getPath());
        $this->assertEquals('test=this', $uri->getQuery());
    }

    public function testPostDispatchResultIsPassedByReferenceToEventListeners()
    {
        $app = $this->setupActionController();

        $app->events()->attach('dispatch', function($e) {
            $result = $e->getResult();
            if (!$result) {
                return;
            }
            $result['foo'] = 'bar';
        });
        $app->events()->attach('dispatch', function($e) {
            $result = $e->getResult();
            if (!$result) {
                return;
            }
            $response = new Response();
            $content  = json_encode($result);
            $response->setContent($content);
            return $response;
        });

        $response = $app->run();
        $response = json_decode($response->getContent());
        $this->assertTrue(isset($response->foo), var_export($response, 1));
        $this->assertEquals('bar', $response->foo);
    }

    public function testDispatchingInjectsLocatorInLocatorAwareControllers()
    {
        $app = new Application();

        $request = new Request();
        $uri     = UriFactory::factory('http://example.local/locator-aware');
        $request->setUri($uri);
        $app->setRequest($request);

        $route = Router\Http\Literal::factory(array(
            'route'    => '/locator-aware',
            'defaults' => array(
                'controller' => 'locator-aware',
            ),
        ));
        $router  = $app->getRouter();
        $router->addRoute('locator-aware', $route);

        $locator = new TestAsset\Locator();
        $locator->add('locator-aware', function() {
            return new TestAsset\LocatorAwareController;
        });
        $app->setLocator($locator);

        $storage = new ArrayObject();
        $events  = StaticEventManager::getInstance();
        $events->attach('ZendTest\Mvc\TestAsset\LocatorAwareController', 'dispatch', function ($e) use ($storage) {
            $controller = $e->getTarget();
            $storage['locator'] = $controller->getLocator();
            return $e->getResponse();
        }, 100);

        $app->run();

        $this->assertTrue(isset($storage['locator']));
        $this->assertSame($locator, $storage['locator']);
    }

    public function testFinishEventIsTriggeredAfterDispatching()
    {
        $app = $this->setupActionController();
        $app->events()->attach('finish', function($e) {
            return $e->getResponse()->setContent($e->getResponse()->getBody() . 'foobar');
        });
        $this->assertContains('foobar', $app->run()->getBody(), 'The "finish" event was not triggered ("foobar" not in response)');
    }

    public function testCanProvideAlternateEventManagerToDisableDefaultRouteAndDispatchEventListeners()
    {
        $app    = $this->setupActionController();
        $events = new EventManager();
        $app->setEventManager($events);

        $listener1 = function($e) {
            $response = $e->getResponse();
            $content  = $response->getContent();
            $content  = (empty($content) ? 'listener1' : $content . '::' . 'listener1');
            $response->setContent($content);
        };
        $listener2 = function($e) {
            $response = $e->getResponse();
            $content  = $response->getContent();
            $content  = (empty($content) ? 'listener2' : $content . '::' . 'listener2');
            $response->setContent($content);
        };
        $events = StaticEventManager::getInstance();
        $events->attach('ZendTest\Mvc\Controller\TestAsset\SampleController', 'dispatch', $listener1, 10);
        $events->attach('ZendTest\Mvc\Controller\TestAsset\SampleController', 'dispatch', $listener2, -10);

        $app->run();
        $response = $app->getResponse();
        $content  = $response->getContent();

        $this->assertNotContains('listener1', $content);
        $this->assertNotContains('listener2', $content);
    }

    /**
     * @group error-handling
     */
    public function testExceptionsRaisedInDispatchableShouldRaiseDispatchErrorEvent()
    {
        $app      = $this->setupBadController();
        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $exception = $e->getParam('exception');
            $response->setContent($exception->getMessage());
            return $response;
        });

        $app->run();
        $this->assertContains('Raised an exception', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testInabilityToRetrieveControllerShouldTriggerDispatchError()
    {
        $app      = $this->setupBadController();
        $app->getLocator()->remove('bad');
        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller);
            return $response;
        });

        $app->run();
        $this->assertContains(Application::ERROR_CONTROLLER_NOT_FOUND, $response->getContent());
        $this->assertContains('bad', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testInvalidControllerTypeShouldTriggerDispatchError()
    {
        $app      = $this->setupBadController();
        $app->getLocator()->add('bad', function() {
            return new stdClass;
        });
        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $controller = $e->getController();
            $class      = $e->getControllerClass();
            $response->setContent("Code: " . $error . '; Controller: ' . $controller . '; Class: ' . $class);
            return $response;
        });

        $app->run();
        $this->assertContains(Application::ERROR_CONTROLLER_INVALID, $response->getContent());
        $this->assertContains('bad', $response->getContent());
        $this->assertContains('stdClass', $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testRoutingFailureShouldTriggerDispatchError()
    {
        $app    = $this->setupBadController();
        $router = new Router\SimpleRouteStack();
        $app->setRouter($router);

        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $app->run();
        $this->assertContains(Application::ERROR_CONTROLLER_NOT_FOUND, $response->getContent());
    }

    /**
     * @group error-handling
     */
    public function testLocatorExceptionShouldTriggerDispatchError()
    {
        $app     = $this->setupPathController();
        $locator = new TestAsset\Locator();
        $app->setLocator($locator);

        $response = new Response();
        $app->events()->attach('dispatch.error', function($e) use ($response) {
            return $response;
        });

        $result = $app->run();
        $this->assertSame($response, $result);
    }

    /**
     * @group error-handling
     */
    public function testFailureForRouteToReturnRouteMatchShouldPopulateEventError()
    {
        $app    = $this->setupBadController();
        $router = new Router\SimpleRouteStack();
        $app->setRouter($router);

        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('dispatch.error', function ($e) use ($response) {
            $error      = $e->getError();
            $response->setContent("Code: " . $error);
            return $response;
        });

        $app->run();
        $event = $app->getMvcEvent();
        $this->assertEquals(Application::ERROR_CONTROLLER_NOT_FOUND, $event->getError());
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfRouteEventReturnsResponse()
    {
        $app      = new Application();
        $response = $app->getResponse();
        $events   = $app->events();
        $events->attach('route', function($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach('finish', function($e) use ($token) {
            $token->foo = 'bar';
        });

        $app->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar',$token->foo);
    }

    /**
     * @group ZF2-171
     */
    public function testFinishShouldRunEvenIfDispatchEventReturnsResponse()
    {
        $app      = new Application();
        $response = $app->getResponse();
        $events   = $app->events();
        $events->clearListeners('route');
        $events->attach('dispatch', function($e) use ($response) {
            return $response;
        }, 100);

        $token = new stdClass;
        $events->attach('finish', function($e) use ($token) {
            $token->foo = 'bar';
        });

        $app->run();
        $this->assertTrue(isset($token->foo));
        $this->assertEquals('bar',$token->foo);
    }
}
