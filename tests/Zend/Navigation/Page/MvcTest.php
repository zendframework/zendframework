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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Navigation\Page;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Helper\Url as UrlHelper,
    Zend\Mvc\Router\RouteMatch,
    Zend\Mvc\Router\Http\Regex as RegexRoute,
    Zend\Mvc\Router\Http\Literal as LiteralRoute,
    Zend\Mvc\Router\Http\TreeRouteStack,
    Zend\Navigation\Page,
    Zend\Navigation,
    ZendTest\Navigation\TestAsset;

/**
 * Tests the class Zend_Navigation_Page_Mvc
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class MvcTest extends TestCase
{
    protected $_front;
    protected $_oldRequest;
    protected $_oldRouter;

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

        $this->urlHelper = new UrlHelper();
        $this->urlHelper->setRouter($this->router);
        $this->urlHelper->setRouteMatch($this->routeMatch);
    }

    protected function tearDown()
    {
    }

    public function testHrefGeneratedByUrlHelperRequiresNoRoute()
    {
        $page = new Page\Mvc(array(
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index'
        ));
        $page->setRouteMatch($this->routeMatch);
        $page->setUrlHelper($this->urlHelper);
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

        $urlHelper = new UrlHelper();
        $urlHelper->setRouter($router);
        $urlHelper->setRouteMatch($routeMatch);

        $page->setUrlHelper($urlHelper);
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

        $urlHelper = new UrlHelper;
        $urlHelper->setRouter($router);
        $urlHelper->setRouteMatch($routeMatch);

        $page->setUrlHelper($urlHelper);
        $page->setRouteMatch($routeMatch);

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseWhenNoRouteAndNoMatchedRouteNameIsSet()
    {
        $page = new Page\Mvc();

        $routeMatch = new RouteMatch(array());
        $this->urlHelper->setRouteMatch($routeMatch);

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
        $page->setUrlHelper($this->urlHelper);

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

        $this->urlHelper->setRouteMatch($routeMatch);

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

        $this->urlHelper->setRouteMatch($routeMatch);

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

        $this->urlHelper->setRouteMatch($routeMatch);

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

        $this->urlHelper->setRouteMatch($routeMatch);

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

        $this->urlHelper->setRouteMatch($routeMatch);

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
            'meaning'    => 42
        );

        $page = new Page\Mvc($options);

        $toArray = $page->toArray();

        $options['route']        = null;
        $options['params']       = array();
        $options['rel']          = array();
        $options['rev']          = array();

        $this->assertEquals(array(),
            array_diff_assoc($options, $page->toArray()));
    }

    public function testSpecifyingAnotherUrlHelperToGenerateHrefs()
    {
        $newHelper = new TestAsset\UrlHelper();

        $page = new Page\Mvc();
        $page->setUrlHelper($newHelper);

        $expected = TestAsset\UrlHelper::RETURN_URL;
        $actual   = $page->getHref();

        $this->assertEquals($expected, $actual);
    }

    public function testDefaultUrlHelperCanBeSetWithConstructor()
    {
        $page = new Page\Mvc(array(
            'label'            => 'foo',
            'action'           => 'index',
            'controller'       => 'index',
            'defaultUrlHelper' => $this->urlHelper
        ));

        $this->assertEquals($this->urlHelper, $page->getDefaultUrlHelper());
        $page->setDefaultUrlHelper(null);
    }

    public function testDefaultUrlHelperCanBeSetWithGetter()
    {
        $page = new Page\Mvc(array(
            'label'            => 'foo',
            'action'           => 'index',
            'controller'       => 'index',
        ));
        $page->setDefaultUrlHelper($this->urlHelper);

        $this->assertEquals($this->urlHelper, $page->getDefaultUrlHelper());
        $page->setDefaultUrlHelper(null);
    }

    public function testNoExceptionForGetHrefIfDefaultUrlHelperIsSet()
    {
        $page = new Page\Mvc(array(
            'label'            => 'foo',
            'action'           => 'index',
            'controller'       => 'index',
            'defaultUrlHelper' => $this->urlHelper
        ));

        // If the default url helper is not used an exception will be thrown.
        // This method intentionally has no assertion.
        $page->getHref();
        $page->setDefaultUrlHelper(null);
    }
}
