<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request as Request;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\Mvc\Router\Http\Part;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\Request as BaseRequest;
use ZendTest\Mvc\Router\FactoryTester;

class PartTest extends TestCase
{
    public static function getRoute()
    {
        $routePlugins = new RoutePluginManager();
        $routePlugins->setInvokableClass('part', 'Zend\Mvc\Router\Http\Part');

        return new Part(
            array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/foo',
                    'defaults' => array(
                        'controller' => 'foo'
                    )
                )
            ),
            true,
            $routePlugins,
            array(
                'bar' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/bar',
                        'defaults' => array(
                            'controller' => 'bar'
                        )
                    )
                ),
                'baz' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route' => '/baz'
                    ),
                    'child_routes' => array(
                        'bat' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/:controller'
                            ),
                            'may_terminate' => true,
                            'child_routes'  => array(
                                'wildcard' => array(
                                    'type' => 'Zend\Mvc\Router\Http\Wildcard'
                                )
                            )
                        )
                    )
                ),
                'bat' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route'    => '/bat[/:foo]',
                        'defaults' => array(
                            'foo' => 'bar'
                        )
                    ),
                    'may_terminate' => true,
                    'child_routes'  => array(
                        'literal' => array(
                            'type'   => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route' => '/bar'
                            )
                        ),
                        'optional' => array(
                            'type'   => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/bat[/:bar]'
                            )
                        ),
                    )
                )
            )
        );
    }

    public static function getRouteAlternative()
    {
        $routePlugins = new RoutePluginManager();
        $routePlugins->setInvokableClass('part', 'Zend\Mvc\Router\Http\Part');

        return new Part(
            array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/[:controller[/:action]]',
                    'defaults' => array(
                        'controller' => 'fo-fo',
                        'action' => 'index'
                    )
                )
            ),
            true,
            $routePlugins,
            array(
                'wildcard' => array(
                    'type' => 'Zend\Mvc\Router\Http\Wildcard',
                    'options' => array(
                        'key_value_delimiter' => '/',
                        'param_delimiter' => '/'
                    )
                ),
                'query' => array(
                    'type' => 'Zend\Mvc\Router\Http\Query',
                    'options' => array(
                        'key_value_delimiter' => '=',
                        'param_delimiter' => '&'
                    )
                )
            )
        );
    }

    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                self::getRoute(),
                '/foo',
                null,
                null,
                array('controller' => 'foo')
            ),
            'offset-skips-beginning' => array(
                self::getRoute(),
                '/bar/foo',
                4,
                null,
                array('controller' => 'foo')
            ),
            'simple-child-match' => array(
                self::getRoute(),
                '/foo/bar',
                null,
                'bar',
                array('controller' => 'bar')
            ),
            'offset-does-not-enable-partial-matching' => array(
                self::getRoute(),
                '/foo/foo',
                null,
                null,
                null
            ),
            'offset-does-not-enable-partial-matching-in-child' => array(
                self::getRoute(),
                '/foo/bar/baz',
                null,
                null,
                null
            ),
            'non-terminating-part-does-not-match' => array(
                self::getRoute(),
                '/foo/baz',
                null,
                null,
                null
            ),
            'child-of-non-terminating-part-does-match' => array(
                self::getRoute(),
                '/foo/baz/bat',
                null,
                'baz/bat',
                array('controller' => 'bat')
            ),
            'parameters-are-used-only-once' => array(
                self::getRoute(),
                '/foo/baz/wildcard/foo/bar',
                null,
                'baz/bat/wildcard',
                array('controller' => 'wildcard', 'foo' => 'bar')
            ),
            'optional-parameters-are-dropped-without-child' => array(
                self::getRoute(),
                '/foo/bat',
                null,
                'bat',
                array('foo' => 'bar')
            ),
            'optional-parameters-are-not-dropped-with-child' => array(
                self::getRoute(),
                '/foo/bat/bar/bar',
                null,
                'bat/literal',
                array('foo' => 'bar')
            ),
            'optional-parameters-not-required-in-last-part' => array(
                self::getRoute(),
                '/foo/bat/bar/bat',
                null,
                'bat/optional',
                array('foo' => 'bar')
            ),
            'simple-match' => array(
                self::getRouteAlternative(),
                '/',
                null,
                null,
                array(
                    'controller' => 'fo-fo',
                    'action' => 'index'
                )
            ),
            'match-wildcard' => array(
                self::getRouteAlternative(),
                '/fo-fo/index/param1/value1',
                null,
                'wildcard',
                array(
                        'controller' => 'fo-fo',
                        'action' => 'index',
                        'param1' => 'value1'
                )
            ),
            'match-query' => array(
                self::getRouteAlternative(),
                '/fo-fo/index?param1=value1',
                0,
                'query',
                array(
                    'controller' => 'fo-fo',
                    'action' => 'index'
                )
            )
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Part    $route
     * @param        string  $path
     * @param        integer $offset
     * @param        string  $routeName
     * @param        array   $params
     */
    public function testMatching(Part $route, $path, $offset, $routeName, array $params = null)
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset);

        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);

            if ($offset === null) {
                $this->assertEquals(strlen($path), $match->getLength());
            }

            $this->assertEquals($routeName, $match->getMatchedRouteName());

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Part    $route
     * @param        string  $path
     * @param        integer $offset
     * @param        string  $routeName
     * @param        array   $params
     */
    public function testAssembling(Part $route, $path, $offset, $routeName, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $result = $route->assemble($params, array('name' => $routeName));

        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }

    public function testAssembleNonTerminatedRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Part route may not terminate');
        self::getRoute()->assemble(array(), array('name' => 'baz'));
    }

    public function testBaseRouteMayNotBePartRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Base route may not be a part route');

        $route = new Part(self::getRoute(), true, new RoutePluginManager());
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = self::getRoute();
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testGetAssembledParams()
    {
        $route = self::getRoute();
        $route->assemble(array('controller' => 'foo'), array('name' => 'baz/bat'));

        $this->assertEquals(array(), $route->getAssembledParams());
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Part',
            array(
                'route'         => 'Missing "route" in options array',
                'route_plugins' => 'Missing "route_plugins" in options array'
            ),
            array(
                'route'         => new \Zend\Mvc\Router\Http\Literal('/foo'),
                'route_plugins' => new RoutePluginManager()
            )
        );
    }

    /**
     * @group ZF2-105
     */
    public function testFactoryShouldAcceptTraversableChildRoutes()
    {
        $children = new ArrayObject(array(
            'create' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route' => 'create',
                    'defaults' => array(
                        'controller' => 'user-admin',
                        'action'     => 'edit',
                    ),
                ),
            ),
        ));
        $options = array(
            'route'        => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/users',
                    'defaults' => array(
                        'controller' => 'Admin\UserController',
                        'action'     => 'index',
                    ),
                ),
            ),
            'route_plugins' => new RoutePluginManager(),
            'may_terminate' => true,
            'child_routes'  => $children,
        );

        $route = Part::factory($options);
        $this->assertInstanceOf('Zend\Mvc\Router\Http\Part', $route);
    }

    /**
     * @group 3711
     */
    public function testPartRouteMarkedAsMayTerminateCanMatchWhenQueryStringPresent()
    {
        $options = array(
            'route' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/resource',
                    'defaults' => array(
                        'controller' => 'ResourceController',
                        'action'     => 'resource',
                    ),
                ),
            ),
            'route_plugins' => new RoutePluginManager(),
            'may_terminate' => true,
            'child_routes'  => array(
                'child' => array(
                    'type' => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route' => '/child',
                        'defaults' => array(
                            'action' => 'child',
                        ),
                    ),
                ),
            ),
        );

        $route = Part::factory($options);
        $request = new Request();
        $request->setUri('http://example.com/resource?foo=bar');
        $query = new Parameters(array('foo' => 'bar'));
        $request->setQuery($query);
        $query = $request->getQuery();

        $match = $route->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $match);
        $this->assertEquals('resource', $match->getParam('action'));
    }

    /**
     * @group 3711
     */
    public function testPartRouteMarkedAsMayTerminateButWithQueryRouteChildWillMatchChildRoute()
    {
        $options = array(
            'route' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/resource',
                    'defaults' => array(
                        'controller' => 'ResourceController',
                        'action'     => 'resource',
                    ),
                ),
            ),
            'route_plugins' => new RoutePluginManager(),
            'may_terminate' => true,
            'child_routes'  => array(
                'query' => array(
                    'type' => 'Zend\Mvc\Router\Http\Query',
                    'options' => array(
                        'defaults' => array(
                            'query' => 'string',
                        ),
                    ),
                ),
            ),
        );

        $route = Part::factory($options);
        $request = new Request();
        $request->setUri('http://example.com/resource?foo=bar');
        $query = new Parameters(array('foo' => 'bar'));
        $request->setQuery($query);
        $query = $request->getQuery();

        $match = $route->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $match);
        $this->assertEquals('string', $match->getParam('query'));
    }
}
