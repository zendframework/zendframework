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
namespace ZendTest\Controller\Router\Route;
use Zend\Controller\Router\Route;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class StaticTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Route\StaticRoute('users/all');
        $values = $route->match('users/all');

        $this->assertInternalType('array', $values);
    }

    public function testStaticMatchFailure()
    {
        $route = new Route\StaticRoute('archive/2006');
        $values = $route->match('users/all');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Route\StaticRoute('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));
        $values = $route->match('users/all');

        $this->assertInternalType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testStaticUTFMatch()
    {
        $route = new Route\StaticRoute('żółć');
        $values = $route->match('żółć');

        $this->assertInternalType('array', $values);
    }

    public function testRootRoute()
    {
        $route = new Route\StaticRoute('/');
        $values = $route->match('');

        $this->assertSame(array(), $values);
    }

    public function testAssemble()
    {
        $route = new Route\StaticRoute('/about');
        $url = $route->assemble();

        $this->assertSame('about', $url);
    }

    public function testGetDefaults()
    {
        $route = new Route\StaticRoute('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $values = $route->getDefaults();

        $this->assertInternalType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testGetDefault()
    {
        $route = new Route\StaticRoute('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $this->assertSame('ctrl', $route->getDefault('controller'));
        $this->assertSame(null, $route->getDefault('bogus'));
    }

    public function testGetInstance()
    {

        $routeConf = array(
            'route' => 'users/all',
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );

        $config = new \Zend\Config\Config($routeConf);
        $route = Route\StaticRoute::getInstance($config);

        $this->assertInstanceOf('Zend\Controller\Router\Route\StaticRoute', $route);

        $values = $route->match('users/all');

        $this->assertSame('ctrl', $values['controller']);

    }

}
