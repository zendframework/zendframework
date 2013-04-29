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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request as Request;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\Mvc\Router\Http\Chain;
use ZendTest\Mvc\Router\FactoryTester;

class ChainTest extends TestCase
{
    public static function getRoute()
    {
        $routePlugins = new RoutePluginManager();

        return new Chain(
            array(
                array(
                    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route'    => '/:controller',
                        'defaults' => array(
                            'controller' => 'foo',
                        ),
                    ),
                ),
                array(
                    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route'    => '/:bar',
                        'defaults' => array(
                            'bar' => 'bar',
                        ),
                    ),
                ),
                array(
                    'type' => 'Zend\Mvc\Router\Http\Wildcard',
                ),
            ),
            $routePlugins
        );
    }

    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                self::getRoute(),
                '/foo/bar',
                null,
                array(
                    'controller' => 'foo',
                    'bar'        => 'bar',
                ),
            ),
            'offset-skips-beginning' => array(
                self::getRoute(),
                '/baz/foo/bar',
                4,
                array(
                    'controller' => 'foo',
                    'bar'        => 'bar',
                ),
            ),
            'parameters-are-used-only-once' => array(
                self::getRoute(),
                '/foo/baz',
                null,
                array(
                    'controller' => 'foo',
                    'bar' => 'baz',
                ),
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Chain   $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testMatching(Chain $route, $path, $offset, array $params = null)
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

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Chain   $route
     * @param        string  $path
     * @param        integer $offset
     * @param        string  $routeName
     * @param        array   $params
     */
    public function testAssembling(Chain $route, $path, $offset, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $result = $route->assemble($params);

        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Chain',
            array(
                'routes'        => 'Missing "routes" in options array',
                'route_plugins' => 'Missing "route_plugins" in options array',
            ),
            array(
                'routes'        => array(),
                'route_plugins' => new RoutePluginManager(),
            )
        );
    }
}
