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

use Zend\Mvc\Router\RoutePluginManager;
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
}
