<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZendTest\Navigation\Page;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\Regex as RegexRoute;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Navigation\Page;
use Zend\Navigation;
use ZendTest\Navigation\TestAsset;

/**
 * Tests the class Zend_Navigation_Page_Mvc
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @group      Zend_Navigation
 */
class MvcTest extends TestCase
{
    protected function setUp()
    {
        $this->route  = new RegexRoute(
            '((?<controller>[^/]+)(/(?<action>[^/]+))?)',
            '/%controller%/%action%',
            array(
                'controller' => 'index',
                'action'     => 'index',
            )
        );
        $this->router = new TreeRouteStack();
        $this->router->addRoute('default', $this->route);

        $this->routeMatch = new RouteMatch(array());
        $this->routeMatch->setMatchedRouteName('default');
    }

    protected function tearDown()
    {
    }

    public function testHrefGeneratedByRouterRequiresNoRoute()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index'
        ));
        $page->setRouteMatch($this->routeMatch);
        $page->setRouter($this->router);
        $page->setAction('view');
        $page->setController('news');

        $this->assertEquals('/news/view', $page->getHref());
    }

    public function testHrefGeneratedIsRouteAware()
    {
        $page = new Page\Mvc(array(
            'label' => 'foo',
            'action' => 'myaction',
            'controller' => 'mycontroller',
            'route' => 'myroute',
            'params' => array(
                'page' => 1337
            )
        ));

        $route  = new RegexRoute(
            '(lolcat/(?<action>[^/]+)/(?<page>\d+))',
            '/lolcat/%action%/%page%',
            array(
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router = new TreeRouteStack();
        $router->addRoute('myroute', $route);

        $routeMatch = new RouteMatch(array(
            'controller' => 'foobar',
            'action'     => 'bazbat',
            'page'       => 1,
        ));

        $page->setRouter($router);
        $page->setRouteMatch($routeMatch);

        $this->assertEquals('/lolcat/myaction/1337', $page->getHref());
    }

    public function testIsActiveReturnsTrueWhenMatchingRoute()
    {
        $page = new Page\Mvc(array(
            'label' => 'spiffyjrwashere',
            'route' => 'lolfish'
        ));

        $route = new LiteralRoute('/lolfish');

        $router = new TreeRouteStack;
        $router->addRoute('lolfish', $route);

        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('lolfish');

        $page->setRouter($router);
        $page->setRouteMatch($routeMatch);

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsTrueWhenMatchingRouteWhileUsingModuleRouteListener()
    {
        $page = new Page\Mvc(array(
            'label' => 'mpinkstonwashere',
            'route' => 'roflcopter',
            'controller' => 'index'
        ));

        $route = new LiteralRoute('/roflcopter');

        $router = new TreeRouteStack;
        $router->addRoute('roflcopter', $route);

        $routeMatch = new RouteMatch(array(
            ModuleRouteListener::MODULE_NAMESPACE => 'Application\Controller',
            'controller' => 'index'
        ));
        $routeMatch->setMatchedRouteName('roflcopter');

        $event = new MvcEvent();
        $event->setRouter($router)
              ->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->onRoute($event);

        $page->setRouter($event->getRouter());
        $page->setRouteMatch($event->getRouteMatch());

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseWhenMatchingRouteButNonMatchingParams()
    {
        $page       = new Page\Mvc(array(
                                   'label'     => 'foo',
                                   'route'     => 'bar',
                                   'action'    => 'baz',
                               ));
        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('bar');
        $routeMatch->setParam('action', 'qux');
        $page->setRouteMatch($routeMatch);

        $this->assertFalse($page->isActive());
    }

    public function testIsActiveReturnsFalseWhenNoRouteAndNoMatchedRouteNameIsSet()
    {
        $page = new Page\Mvc();

        $routeMatch = new RouteMatch(array());
        $page->setRouteMatch($routeMatch);

        $this->assertFalse($page->isActive());
    }

    /**
     * @group ZF-8922
     */
    public function testGetHrefWithFragmentIdentifier()
    {
        $page = new Page\Mvc(array(
            'label'              => 'foo',
            'fragment' => 'qux',
            'controller'         => 'mycontroller',
            'action'             => 'myaction',
            'route'              => 'myroute',
            'params'             => array(
                'page' => 1337
            )
        ));

        $route = new RegexRoute(
            '(lolcat/(?<action>[^/]+)/(?<page>\d+))',
            '/lolcat/%action%/%page%',
            array(
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1,
            )
        );
        $this->router->addRoute('myroute', $route);
        $this->routeMatch->setMatchedRouteName('myroute');

        $page->setRouteMatch($this->routeMatch);
        $page->setRouter($this->router);

        $this->assertEquals('/lolcat/myaction/1337#qux', $page->getHref());
    }

    public function testIsActiveReturnsTrueOnIdenticalControllerAction()
    {
        $page = new Page\Mvc(array(
            'action'     => 'index',
            'controller' => 'index'
        ));

        $routeMatch = new RouteMatch(array(
            'controller' => 'index',
            'action'     => 'index',
        ));

        $page->setRouteMatch($routeMatch);

        $this->assertTrue($page->isActive());
    }

    public function testIsActiveReturnsFalseOnDifferentControllerAction()
    {
        $page = new Page\Mvc(array(
            'action'     => 'bar',
            'controller' => 'index'
        ));

        $routeMatch = new RouteMatch(array(
            'controller' => 'index',
            'action'     => 'index',
        ));

        $page->setRouteMatch($routeMatch);

        $this->assertFalse($page->isActive());
    }

    public function testIsActiveReturnsTrueOnIdenticalIncludingPageParams()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'view',
            'controller' => 'post',
            'params'     => array(
                'id'     => '1337'
            )
        ));

        $routeMatch = new RouteMatch(array(
            'controller' => 'post',
            'action'     => 'view',
            'id'         => '1337'
        ));

        $page->setRouteMatch($routeMatch);

        $this->assertTrue($page->isActive());
    }

    public function testIsActiveReturnsTrueWhenRequestHasMoreParams()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'view',
            'controller' => 'post',
        ));

        $routeMatch = new RouteMatch(array(
            'controller' => 'post',
            'action'     => 'view',
            'id'         => '1337',
        ));

        $page->setRouteMatch($routeMatch);

        $this->assertTrue($page->isActive());
    }

    public function testIsActiveReturnsFalseWhenRequestHasLessParams()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'view',
            'controller' => 'post',
            'params'     => array(
                'id'     => '1337'
            )
        ));

        $routeMatch = new RouteMatch(array(
            'controller' => 'post',
            'action'     => 'view',
            'id'         => null
        ));

        $page->setRouteMatch($routeMatch);

        $this->assertFalse($page->isActive());
    }

    public function testActionAndControllerAccessors()
    {
        $page = new Page\Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $props = array('Action', 'Controller');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', '', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend\Navigation\Exception\InvalidArgumentException was thrown';
                    $this->fail($msg);
                } catch (Navigation\Exception\InvalidArgumentException $e) {

                }
            }
        }
    }

    public function testRouteAccessor()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index'
        ));

        $props    = array('Route');
        $valids   = array('index', 'help', 'home', 'default', '1', ' ', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg  = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend\Navigation\Exception\InvalidArgumentException was thrown';
                    $this->fail($msg);
                } catch (Navigation\Exception\InvalidArgumentException $e) {

                }
            }
        }
    }

    public function testSetAndGetParams()
    {
        $page = new Page\Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $params = array('foo' => 'bar', 'baz' => 'bat');

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams();
        $this->assertEquals(array(), $page->getParams());

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams(array());
        $this->assertEquals(array(), $page->getParams());
    }

    public function testToArrayMethod()
    {
        $options = array(
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index',
            'fragment'   => 'bar',
            'id'         => 'my-id',
            'class'      => 'my-class',
            'title'      => 'my-title',
            'target'     => 'my-target',
            'order'      => 100,
            'active'     => true,
            'visible'    => false,
            'foo'        => 'bar',
            'meaning'    => 42,
            'router'     => $this->router,
            'route_match' => $this->routeMatch,
        );

        $page = new Page\Mvc($options);

        $toArray = $page->toArray();

        $options['route']  = null;
        $options['params'] = array();
        $options['rel']    = array();
        $options['rev']    = array();

        $options['privilege'] = null;
        $options['resource']  = null;
        $options['pages']     = array();
        $options['type']      = 'Zend\Navigation\Page\Mvc';

        ksort($options);
        ksort($toArray);
        $this->assertEquals($options, $toArray);
    }

    public function testSpecifyingAnotherUrlHelperToGenerateHrefs()
    {
        $newRouter = new TestAsset\Router();

        $page = new Page\Mvc(array(
            'route' => 'default'
        ));
        $page->setRouter($newRouter);

        $expected = TestAsset\Router::RETURN_URL;
        $actual   = $page->getHref();

        $this->assertEquals($expected, $actual);
    }

    public function testDefaultRouterCanBeSetWithConstructor()
    {
        $page = new Page\Mvc(array(
            'label'         => 'foo',
            'action'        => 'index',
            'controller'    => 'index',
            'defaultRouter' => $this->router
        ));

        $this->assertEquals($this->router, $page->getDefaultRouter());
        $page->setDefaultRouter(null);
    }

    public function testDefaultRouterCanBeSetWithGetter()
    {
        $page = new Page\Mvc(array(
            'label'            => 'foo',
            'action'           => 'index',
            'controller'       => 'index',
        ));
        $page->setDefaultRouter($this->router);

        $this->assertEquals($this->router, $page->getDefaultRouter());
        $page->setDefaultRouter(null);
    }

    public function testNoExceptionForGetHrefIfDefaultRouterIsSet()
    {
        $page = new Page\Mvc(array(
            'label'            => 'foo',
            'action'           => 'index',
            'controller'       => 'index',
            'route'            => 'default',
            'defaultRouter'    => $this->router
        ));

        // If the default router is not used an exception will be thrown.
        // This method intentionally has no assertion.
        $page->getHref();
        $page->setDefaultRouter(null);
    }
}
