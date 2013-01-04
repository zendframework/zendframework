<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
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

    public function testRouteMatchIsTransformedToProperControllerClassName()
    {
        $moduleListener = new ModuleRouteListener();
        $this->events->attach($moduleListener);

        $this->router->addRoute('foo', array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/foo',
                'defaults' => array(
                    ModuleRouteListener::MODULE_NAMESPACE => 'Foo',
                    'controller' => 'some-index',
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
        $this->assertEquals('Foo\SomeIndex', $matches->getParam('controller'));
        $this->assertEquals('some-index', $matches->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER));
    }
}
