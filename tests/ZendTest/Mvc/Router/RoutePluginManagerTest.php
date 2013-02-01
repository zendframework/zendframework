<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router;

use Zend\Di\Di;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Mvc_Router
 * @subpackage UnitTests
 * @group      Zend_Router
 */
class RoutePluginManagerTest extends TestCase
{
    public function testLoadNonExistentRoute()
    {
        $routes = new RoutePluginManager();
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $routes->get('foo');
    }

    public function testCanLoadAnyRoute()
    {
        $routes = new RoutePluginManager();
        $routes->setInvokableClass('DummyRoute', 'ZendTest\Mvc\Router\TestAsset\DummyRoute');
        $route = $routes->get('DummyRoute');

        $this->assertInstanceOf('ZendTest\Mvc\Router\TestAsset\DummyRoute', $route);
    }

    public function shippedRoutes()
    {
        return array(
            'hostname' => array('Zend\Mvc\Router\Http\Hostname', array('route' => 'example.com')),
            'literal'  => array('Zend\Mvc\Router\Http\Literal', array('route' => '/example')),
            'regex'    => array('Zend\Mvc\Router\Http\Regex', array('regex' => '[a-z]+', 'spec' => '%s')),
            'scheme'   => array('Zend\Mvc\Router\Http\Scheme', array('scheme' => 'http')),
            'segment'  => array('Zend\Mvc\Router\Http\Segment', array('route' => '/:segment')),
            'wildcard' => array('Zend\Mvc\Router\Http\Wildcard', array()),
            'query'    => array('Zend\Mvc\Router\Http\Query', array()),
            'method'   => array('Zend\Mvc\Router\Http\Method', array('verb' => 'GET')),
        );
    }

    /**
     * @dataProvider shippedRoutes
     */
    public function testDoesNotInvokeDiForShippedRoutes($routeName, $options)
    {
        // Setup route plugin manager
        $routes = new RoutePluginManager();
        foreach ($this->shippedRoutes() as $name => $info) {
            $routes->setInvokableClass($name, $info[0]);
        }

        // Add DI abstract factory
        $di                = new Di;
        $diAbstractFactory = new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI);
        $routes->addAbstractFactory($diAbstractFactory);

        $this->assertTrue($routes->has($routeName));

        try {
            $route = $routes->get($routeName, $options);
            $this->assertInstanceOf($routeName, $route);
        } catch (\Exception $e) {
            $messages = array();
            do {
                $messages[] = $e->getMessage() . "\n" . $e->getTraceAsString();
            } while ($e = $e->getPrevious());
            $this->fail(implode("\n\n", $messages));
        }
    }

    /**
     * @dataProvider shippedRoutes
     */
    public function testDoesNotInvokeDiForShippedRoutesUsingShortName($routeName, $options)
    {
        // Setup route plugin manager
        $routes = new RoutePluginManager();
        foreach ($this->shippedRoutes() as $name => $info) {
            $routes->setInvokableClass($name, $info[0]);
        }

        // Add DI abstract factory
        $di                = new Di;
        $diAbstractFactory = new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI);
        $routes->addAbstractFactory($diAbstractFactory);

        $shortName = substr($routeName, strrpos($routeName, '\\') + 1);

        $this->assertTrue($routes->has($shortName));

        try {
            $route = $routes->get($shortName, $options);
            $this->assertInstanceOf($routeName, $route);
        } catch (\Exception $e) {
            $messages = array();
            do {
                $messages[] = $e->getMessage() . "\n" . $e->getTraceAsString();
            } while ($e = $e->getPrevious());
            $this->fail(implode("\n\n", $messages));
        }
    }
}
