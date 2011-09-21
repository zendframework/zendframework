<?php

namespace Zend\Mvc;

use ArrayObject,
    PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Di\DependencyInjector,
    Zend\Di\ServiceLocator,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
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

        $route = new Router\Http\Literal(array(
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

        $route = new Router\Http\Literal(array(
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

    public function testControllerIsDispatchedDuringRun()
    {
        $app = $this->setupPathController();

        $response = $app->run()->getResponse();
        $this->assertContains('PathController', $response->getContent());
        $this->assertContains('dispatch', $response->getContent());
    }

    public function testDefaultRequestObjectContainsPhpEnvironmentContainers()
    {
        $app = new Application();
        $request = $app->getRequest();
        $query = $request->query();
        $this->assertInstanceOf('Zend\Mvc\PhpEnvironment\GetContainer', $query);
        $post = $request->post();
        $this->assertInstanceOf('Zend\Mvc\PhpEnvironment\PostContainer', $post);
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
        $_SERVER['REQUEST_URI'] = 'http://framework.zend.com/api/zf-version?test=this';
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

        $response = $app->run()->getResponse();
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

        $route = new Router\Http\Literal(array(
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
        $events->attach('Zend\Mvc\TestAsset\LocatorAwareController', 'dispatch', function ($e) use ($storage) {
            $controller = $e->getTarget();
            $storage['locator'] = $controller->getLocator();
            return $e->getResponse();
        }, 100);

        $app->run();

        $this->assertTrue(isset($storage['locator']));
        $this->assertSame($locator, $storage['locator']);
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
        $events->attach('Zend\Mvc\Controller\TestAsset\SampleController', 'dispatch', $listener1, 10);
        $events->attach('Zend\Mvc\Controller\TestAsset\SampleController', 'dispatch', $listener2, -10);

        $app->run();
        $response = $app->getResponse();
        $content  = $response->getContent();

        $this->assertNotContains('listener1', $content);
        $this->assertNotContains('listener2', $content);
    }
}
