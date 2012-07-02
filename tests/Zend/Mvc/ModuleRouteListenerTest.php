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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\RouteListener;
use Zend\Mvc\Router;

class ModuleRouteListenerTest extends TestCase
{
    public function setUp()
    {
        $this->request             = new Request();
        $this->events              = new EventManager();
        $this->router              = new Router\Http\TreeRouteStack();
        $this->routeListener       = new RouteListener();
        $this->moduleRouteListener = new ModuleRouteListener();

        $this->events->attach($this->routeListener);
        $this->events->attach($this->moduleRouteListener, -1);
    }

    public function testRouteReturningModuleNamespaceInRouteMatchTriggersControllerRename()
    {
        $this->router->addRoute('foo', array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/foo',
                'defaults' => array(
                    ModuleRouteListener::MODULE_NAMESPACE => 'Foo',
                    'controller' => 'Index',
                ),
            ),
        ));
        $this->request->setUri('/foo');
        $event = new MvcEvent();
        $event->setRouter($this->router);
        $event->setRequest($this->request);
        $this->events->trigger('route', $event);

        $matches = $event->getRouteMatch();
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $matches);
        $this->assertEquals('Foo\Index', $matches->getParam('controller'));
        $this->assertEquals('Index', $matches->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER));
    }

    public function testRouteNotReturningModuleNamespaceInRouteMatchLeavesControllerUntouched()
    {
        $this->router->addRoute('foo', array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/foo',
                'defaults' => array(
                    'controller' => 'Index',
                ),
            ),
        ));
        $this->request->setUri('/foo');
        $event = new MvcEvent();
        $event->setRouter($this->router);
        $event->setRequest($this->request);
        $this->events->trigger('route', $event);

        $matches = $event->getRouteMatch();
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $matches);
        $this->assertEquals('Index', $matches->getParam('controller'));
    }

    public function testMultipleRegistrationShouldNotResultInMultiplePrefixingOfControllerName()
    {
        $moduleListener = new ModuleRouteListener();
        $this->events->attach($moduleListener);

        $this->router->addRoute('foo', array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/foo',
                'defaults' => array(
                    ModuleRouteListener::MODULE_NAMESPACE => 'Foo',
                    'controller' => 'Index',
                ),
            ),
        ));
        $this->request->setUri('/foo');
        $event = new MvcEvent();
        $event->setRouter($this->router);
        $event->setRequest($this->request);
        $this->events->trigger('route', $event);

        $matches = $event->getRouteMatch();
        $this->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $matches);
        $this->assertEquals('Foo\Index', $matches->getParam('controller'));
        $this->assertEquals('Index', $matches->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER));
    }
}
