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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Router;

use Zend\Config,
    Zend\Controller,
    Zend\Controller\Router\Route,
    Zend\Controller\Router,
    Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class RewriteTest extends \PHPUnit_Framework_TestCase
{
    protected $_router;

    public function setUp() {
        $this->_router = new Router\Rewrite();
        $front = Controller\Front::getInstance();
        $front->resetInstance();
        $front->setDispatcher(new Dispatcher());
        $front->setRequest(new Request());
        $this->_router->setFrontController($front);
    }

    public function tearDown() {
        unset($this->_router);
    }

    public function testAddRoute()
    {
        $this->_router->addRoute('archive', new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $this->_router->getRoutes();

        $this->assertEquals(1, count($routes));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $routes['archive']);

        $this->_router->addRoute('register', new Route\Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $this->_router->getRoutes();

        $this->assertEquals(2, count($routes));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $routes['register']);
    }

    public function testAddRoutes()
    {
        $routes = array(
            'archive' => new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')),
            'register' => new Route\Route('register/:action', array('controller' => 'profile', 'action' => 'register'))
        );
        $this->_router->addRoutes($routes);

        $values = $this->_router->getRoutes();

        $this->assertEquals(2, count($values));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $values['archive']);
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $values['register']);
    }

    public function testHasRoute()
    {
        $this->_router->addRoute('archive', new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));

        $this->assertEquals(true, $this->_router->hasRoute('archive'));
        $this->assertEquals(false, $this->_router->hasRoute('bogus'));
    }

    public function testGetRoute()
    {
        $archive = new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $this->_router->addRoute('archive', $archive);

        $route = $this->_router->getRoute('archive');

        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $route);
        $this->assertSame($route, $archive);
    }

    public function testRemoveRoute()
    {
        $this->_router->addRoute('archive', new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));

        $route = $this->_router->getRoute('archive');

        $this->_router->removeRoute('archive');

        $routes = $this->_router->getRoutes();
        $this->assertEquals(0, count($routes));

        try {
            $route = $this->_router->removeRoute('archive');
        } catch (Router\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testGetNonExistentRoute()
    {
        try {
            $route = $this->_router->getRoute('bogus');
        } catch (Router\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testRoute()
    {
        $request = new Request();

        $token = $this->_router->route($request);

        $this->assertInstanceOf('Zend\Controller\Request\Http', $token);
    }

    public function testRouteWithIncorrectRequest()
    {
        $request = new Incorrect();

        try {
            $token = $this->_router->route($request);
            $this->fail('Should throw an Exception');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
        }
    }

    public function testDefaultRoute()
    {
        $request = new Request();

        $token = $this->_router->route($request);

        $routes = $this->_router->getRoutes();
        $this->assertInstanceOf('Zend\Controller\Router\Route\Module', $routes['application']);
    }

    public function testDefaultRouteWithEmptyAction()
    {
        $request = new Request('http://localhost/ctrl');

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('defact', $token->getActionName());
    }

    public function testEmptyRoute()
    {
        $request = new Request('http://localhost/');

        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('empty', new Route\Route('', array('controller' => 'ctrl', 'action' => 'act')));

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act', $token->getActionName());
    }

    public function testEmptyPath()
    {
        $request = new Request('http://localhost/');

        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('catch-all', new Route\Route(':controller/:action/*', array('controller' => 'ctrl', 'action' => 'act')));

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act', $token->getActionName());
    }

    public function testEmptyPathWithWildcardRoute()
    {
        $request = new Request('http://localhost/');

        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('catch-all', new Route\Route('*', array('controller' => 'ctrl', 'action' => 'act')));

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act', $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $request = new Request('http://localhost/archive/action/bogus');

        $this->_router->addRoute('application', new Route\Route(':controller/:action'));

        try {
            $token = $this->_router->route($request);
            $this->fail('An expected Zend\Controller\Router\Exception was not raised');
        } catch (Router\Exception $expected) {
            $this->assertEquals('No route matched the request', $expected->getMessage());
        }

        $this->assertNull($request->getControllerName());
        $this->assertNull($request->getActionName());
    }

    public function testDefaultRouteMatched()
    {
        $request = new Request('http://localhost/ctrl/act');

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act', $token->getActionName());
    }

    public function testDefaultRouteMatchedWithControllerOnly()
    {
        $request = new Request('http://localhost/ctrl');

        $token = $this->_router->route($request);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('defact', $token->getActionName());
    }

    public function testFirstRouteMatched()
    {
        $request = new Request('http://localhost/archive/2006');

        $this->_router->addRoute('archive', new Route\Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $this->_router->addRoute('register', new Route\Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $token = $this->_router->route($request);

        $this->assertEquals('archive', $token->getControllerName());
        $this->assertEquals('show', $token->getActionName());
    }

    public function testGetCurrentRoute()
    {
        $request = new Request('http://localhost/ctrl/act');

        try {
            $route = $this->_router->getCurrentRoute();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
        }

        try {
            $route = $this->_router->getCurrentRouteName();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
        }

        $token = $this->_router->route($request);

        try {
            $route = $this->_router->getCurrentRoute();
            $name = $this->_router->getCurrentRouteName();
        } catch (\Exception $e) {
            $this->fail('Current route is not set');
        }

        $this->assertEquals('application', $name);
        $this->assertInstanceOf('Zend\Controller\Router\Route\Module', $route);
    }

    public function testAddConfig()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'routes.ini';
        $config = new Config\Ini($file, 'testing');

        $this->_router->addConfig($config, 'routes');

        $this->assertInstanceOf('Zend\Controller\Router\Route\StaticRoute', $this->_router->getRoute('news'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $this->_router->getRoute('archive'));

        try {
            $this->_router->addConfig($config, 'database');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Controller\Router\Exception', $e);
            return true;
        }
    }

    public function testAddConfigWithoutSection()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'routes.ini';
        $config = new Config\Ini($file, 'testing');

        $this->_router->addConfig($config->routes);

        $this->assertInstanceOf('Zend\Controller\Router\Route\StaticRoute', $this->_router->getRoute('news'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $this->_router->getRoute('archive'));
    }

    public function testAddConfigWithRootNode()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'routes-root.ini';
        $config = new Config\Ini($file, 'routes');

        $this->_router->addConfig($config);

        $this->assertInstanceOf('Zend\Controller\Router\Route\StaticRoute', $this->_router->getRoute('news'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Route', $this->_router->getRoute('archive'));
    }

    public function testRemoveDefaultRoutes()
    {
        $request = new Request('http://localhost/ctrl/act');
        $this->_router->removeDefaultRoutes();

        try {
            $token = $this->_router->route($request);
            $this->fail('An expected Zend\Controller\Router\Exception was not raised');
        } catch (Router\Exception $expected) {
            $this->assertEquals('No route matched the request', $expected->getMessage());
        }

        $routes = $this->_router->getRoutes();
        $this->assertEquals(0, count($routes));
    }

    public function testDefaultRouteMatchedWithModules()
    {
        Controller\Front::getInstance()->setControllerDirectory(array(
            'default' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        $request = new Request('http://localhost/mod/ctrl/act');
        $token = $this->_router->route($request);

        $this->assertEquals('mod',  $token->getModuleName());
        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act',  $token->getActionName());
    }

    public function testRouteCompatDefaults()
    {
        $request = new Request('http://localhost/');

        $token = $this->_router->route($request);

        $this->assertEquals('application', $token->getModuleName());
        $this->assertEquals('defctrl', $token->getControllerName());
        $this->assertEquals('defact', $token->getActionName());
    }

    public function testDefaultRouteWithEmptyControllerAndAction()
    {
        Controller\Front::getInstance()->setControllerDirectory(array(
            'application' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        $request = new Request('http://localhost/mod');

        $token = $this->_router->route($request);

        $this->assertEquals('mod', $token->getModuleName());
        $this->assertEquals('defctrl', $token->getControllerName());
        $this->assertEquals('defact', $token->getActionName());
    }

    public function testNumericallyIndexedReturnParams()
    {
        $request = new Request('http://localhost/archive/2006');

        $this->_router->addRoute('test', new Mockup1());

        $token = $this->_router->route($request);

        $this->assertEquals('index', $token->getControllerName());
        $this->assertEquals('index', $token->getActionName());
        $this->assertEquals('first_parameter_value', $token->getParam(0));
    }

    public function testUrlValuesHandling1() // See ZF-3212 and ZF-3219
    {
        $this->_router->addRoute('foo', new Route\Route(':lang/foo', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));
        $this->_router->addRoute('bar', new Route\Route(':lang/bar', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));

        $request = new Request('http://localhost/nl/bar');
        $token = $this->_router->route($request);

        $this->assertEquals('nl/foo', $this->_router->getRoute('foo')->assemble());
        $this->assertEquals('nl/bar', $this->_router->getRoute('bar')->assemble());
    }

    public function testUrlValuesHandling2() // See ZF-3212 and ZF-3219
    {
        $this->_router->addRoute('foo', new Route\Route(':lang/foo', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));
        $this->_router->addRoute('bar', new Route\Route(':lang/bar', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));

        $request = new Request('http://localhost/en/foo');
        $token = $this->_router->route($request);

        $this->assertEquals('en/foo', $this->_router->getRoute('foo')->assemble());
        $this->assertEquals('nl/bar', $this->_router->getRoute('bar')->assemble());
    }

    public function testUrlValuesHandling3() // See ZF-3212 and ZF-3219
    {
        $this->_router->addRoute('foo', new Route\Route(':lang/foo', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));
        $this->_router->addRoute('bar', new Route\Route(':lang/bar', array('lang' => 'nl', 'controller' => 'index', 'action' => 'index')));

        $request = new Request('http://localhost/en/bar');
        $token = $this->_router->route($request);

        $this->assertEquals('nl/foo', $this->_router->getRoute('foo')->assemble());
        $this->assertEquals('en/bar', $this->_router->getRoute('bar')->assemble());
    }

    public function testRouteRequestInterface()
    {
        $request = new Request('http://localhost/en/foo');
        $front = $this->_router->getFrontController()->setRequest($request);

        $this->_router->addRoute('req', new Mockup2());
        $routeRequest = $this->_router->getRoute('req')->getRequest();

        $this->assertInstanceOf('Zend\Controller\Request\AbstractRequest', $request);
        $this->assertInstanceOf('Zend\Controller\Request\AbstractRequest', $routeRequest);
        $this->assertSame($request, $routeRequest);
    }

    public function testRoutingVersion2Routes()
    {
        $request = new Request('http://localhost/en/bar');
        $request->setParam('path', 'v2test');

        $route = new Stub('not-important');
        $this->_router->addRoute('foo', $route);

        $token = $this->_router->route($request);

        $this->assertEquals('v2test', $token->getParam('path'));
    }

    public function testRoutingChainedRoutes()
    {
        $request = new Request('http://localhost/foo/bar');

        $foo = new Route\Route('foo', array('foo' => true));
        $bar = new Route\Route('bar', array('bar' => true, 'controller' => 'foo', 'action' => 'bar'));

        $chain = new Route\Chain();
        $chain->addChain($foo)->addChain($bar);

        $this->_router->addRoute('foo-bar', $chain);

        $token = $this->_router->route($request);

        $this->assertEquals('foo', $token->getControllerName());
        $this->assertEquals('bar', $token->getActionName());
        $this->assertEquals(true, $token->getParam('foo'));
        $this->assertEquals(true, $token->getParam('bar'));
    }

    public function testRouteWithHostnameChain()
    {
        $request = new Request('http://www.zend.com/bar');

        $foo = new Route\Hostname('nope.zend.com', array('module' => 'nope-bla', 'bogus' => 'bogus'));
        $bar = new Route\Hostname('www.zend.com', array('module' => 'www-bla'));

        $bla = new Route\StaticRoute('bar', array('controller' => 'foo', 'action' => 'bar'));

        $chainMatch = new Route\Chain();
        $chainMatch->addChain($bar)->addChain($bla);

        $chainNoMatch = new Route\Chain();
        $chainNoMatch->addChain($foo)->addChain($bla);

        $this->_router->addRoute('match',    $chainMatch);
        $this->_router->addRoute('no-match', $chainNoMatch);

        $token = $this->_router->route($request);

        $this->assertEquals('www-bla', $token->getModuleName());
        $this->assertEquals('foo', $token->getControllerName());
        $this->assertEquals('bar', $token->getActionName());
        $this->assertNull($token->getParam('bogus'));
    }

    public function testAssemblingWithHostnameHttp()
    {
        $route = new Route\Hostname('www.zend.com');

        $this->_router->addRoute('hostname-route', $route);

        $this->assertEquals('http://www.zend.com', $this->_router->assemble(array(), 'hostname-route'));
    }

    public function testAssemblingWithHostnameHttps()
    {
        $backupServer = $_SERVER;
        $_SERVER['HTTPS'] = 'on';

        $route = new Route\Hostname('www.zend.com');

        $this->_router->addRoute('hostname-route', $route);

        $this->assertEquals('https://www.zend.com', $this->_router->assemble(array(), 'hostname-route'));

        $_SERVER = $backupServer;
    }

    public function testAssemblingWithHostnameThroughChainHttp()
    {
        $foo = new Route\Hostname('www.zend.com');
        $bar = new Route\StaticRoute('bar');

        $chain = new Route\Chain();
        $chain->addChain($foo)->addChain($bar);

        $this->_router->addRoute('foo-bar', $chain);

        $this->assertEquals('http://www.zend.com/bar', $this->_router->assemble(array(), 'foo-bar'));
    }

    public function testAssemblingWithHostnameWithChainHttp()
    {
        $foo = new Route\Hostname('www.zend.com');
        $bar = new Route\StaticRoute('bar');

        $chain = $foo->addChain($bar);

        $this->_router->addRoute('foo-bar', $chain);

        $this->assertEquals('http://www.zend.com/bar', $this->_router->assemble(array(), 'foo-bar'));
    }

    public function testAssemblingWithNonFirstHostname()
    {
        $this->markTestSkipped('Router features not ready');

        $foo = new Route\StaticRoute('bar');
        $bar = new Route\Hostname('www.zend.com');

        $foo->addChain($bar);

        $this->_router->addRoute('foo-bar', $foo);

        $this->assertEquals('bar/www.zend.com', $this->_router->assemble(array(), 'foo-bar'));
    }

    /**
     * @see ZF-3922
     */
    public function testRouteShouldMatchEvenWithTrailingSlash()
    {
        $route = new Route\Route(
            'blog/articles/:id',
            array(
                'controller' => 'blog',
                'action'     => 'articles',
                'id'         => 0,
            ),
            array(
                'id' => '[0-9]+',
            )
        );
        $this->_router->addRoute('article-id', $route);

        $request = new Request('http://localhost/blog/articles/2006/');
        $token   = $this->_router->route($request);

        $this->assertSame('article-id', $this->_router->getCurrentRouteName());

        $this->assertEquals('2006', $token->getParam('id', false));
    }

    public function testGlobalParam()
    {
        $route = new Route\Route(
            ':lang/articles/:id',
            array(
                'controller' => 'blog',
                'action'     => 'articles',
                'id'         => 0,
            )
        );
        $this->_router->addRoute('article-id', $route);
        $this->_router->setGlobalParam('lang', 'de');

        $url = $this->_router->assemble(array('id' => 1), 'article-id');

        $this->assertEquals('/de/articles/1', $url);
    }

    public function testGlobalParamOverride()
    {
        $route = new Route\Route(
            ':lang/articles/:id',
            array(
                'controller' => 'blog',
                'action'     => 'articles',
                'id'         => 0,
            )
        );
        $this->_router->addRoute('article-id', $route);
        $this->_router->setGlobalParam('lang', 'de');

        $url = $this->_router->assemble(array('id' => 1, 'lang' => 'en'), 'article-id');

        $this->assertEquals('/en/articles/1', $url);
    }

    public function testChainNameSeparatorIsSetCorrectly() {
        $separators = array('_','unitTestSeparator','-');
        $results = array();

        foreach($separators as $separator) {
            $this->_router->setChainNameSeparator($separator);
            $results[] = $this->_router->getChainNameSeparator();
        }

        $this->assertEquals($separators, $results);
    }

    public function testChainNameSeparatorisUsedCorrectly() {
        $config = new Config\Config(array('chains' => array(
            'type'=>'Zend\Controller\Router\Route\StaticRoute',
            'route'=>'foo',
            'chains'=> array('bar'=>
                array('type'=>'Zend\Controller\Router\Route\StaticRoute',
                    'route'=>'bar',
                    'defaults'=>array(
                    'module'=>'module',
                    'controller'=>'controller',
                    'action'=>'action'))))));
        $this->_router->setChainNameSeparator('_separator_')
                      ->addConfig($config);
        $url = $this->_router->assemble(array(),'chains_separator_bar');
        $this->assertEquals('/foo/bar',$url);
    }

    public function testRequestParamsUsedAsGlobalParam()
    {
        $route = new Route\Route(
            '/articles/:id',
            array(
                'controller' => 'blog',
                'action'     => 'articles',
            )
        );

        $request = Controller\Front::getInstance()->getRequest();
        $request->setParam('id', 777);

        $this->_router->addRoute('article-id', $route);
        $this->_router->useRequestParametersAsGlobal(true);
        $this->_router->route($request);

        $url = $this->_router->assemble(array(), 'article-id');

        $this->assertEquals('/articles/777', $url);
    }
    

    /**
     * Test that it is possible to generate a URL with a numerical key
     *
     * @since  2010-06-11
     * @group  ZF-8914
     * @covers Zend_Controller_Router_Rewrite::assemble
     */
    public function testCanGenerateNumericKeyUri()
    {
        $this->_router->addRoute(
            'application', 
            new Route\Route(
                ':controller/:action/*',
                array('controller' => 'index', 'action' => 'index')
            )
       );

       $params = array(
            'controller' => 'index',
            'action'     => 'index',
            '2'          => 'foo',
            'page'       => 'bar',
        );

        $this->assertEquals(
            '/index/index/2/foo/page/bar',
            $this->_router->assemble($params)
        );
    }
}


/**
 * Zend_Controller_Router_RewriteTest_Request - request object for router testing
 *
 * @uses Zend_Controller_Request_Interface
 */
class Request extends \Zend\Controller\Request\Http
{
    protected $_host;
    protected $_port;

    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        $url = UriFactory::factory($uri, 'http');
        $this->_host = $url->getHost();
        $this->_port = $url->getPort();

        parent::__construct($url);
    }

    public function getHttpHost() {
        $return = $this->_host;
        if ($this->_port)  $return .= ':' . $this->_port;
        return $return;
    }
}

/**
 * Zend_Controller_RouterTest_Dispatcher
 */
class Dispatcher extends \Zend\Controller\Dispatcher\Standard
{
    public function getDefaultControllerName()
    {
        return 'defctrl';
    }

    public function getDefaultAction()
    {
        return 'defact';
    }
}

/**
 * Zend_Controller_RouterTest_Request_Incorrect - request object for router testing
 *
 * @uses Zend_Controller_Request_Abstract
 */
class Incorrect extends \Zend\Controller\Request\AbstractRequest
{
}

/**
 * Zend_Controller_RouterTest_RouteV2_Stub - request object for router testing
 *
 * @uses Zend_Controller_Request_Abstract
 */
class Stub extends Route\AbstractRoute
{
    public function match($request) {
        return array('path', $request->getParam('path'));
    }

    public static function getInstance(Config\Config $config) {}
    public function assemble($data = array(), $reset = false, $encode = false) {}
}

class Mockup1 implements Route
{
    public function match($path, $partial = null)
    {
        return array(
            "controller" => "index",
            "action" => "index",
            0 => "first_parameter_value"
        );
    }
    public static function getInstance(Config\Config $config) {}
    public function assemble($data = array(), $reset = false, $encode = false) {}
}

class Mockup2 implements Route
{
    protected $_request;

    public function match($path, $partial = null) {}
    public static function getInstance(Config\Config $config) {}
    public function assemble($data = array(), $reset = false, $encode = false) {}

    public function setRequest($request) {
        $this->_request = $request;
    }
    public function getRequest() {
        return $this->_request;
    }
}
