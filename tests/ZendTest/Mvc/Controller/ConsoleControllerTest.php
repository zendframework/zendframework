<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class ConsoleControllerTest extends TestCase
{
    /**
     * @var TestAsset\ConsoleController
     */
    public $controller;

    public function setUp()
    {
        $this->controller = new TestAsset\ConsoleController();
        $routeMatch = new RouteMatch(array('controller' => 'controller-sample'));
        $event      = new MvcEvent();
        $event->setRouteMatch($routeMatch);
        $this->controller->setEvent($event);
    }

    public function testDispatchCorrectRequest()
    {
        $request = new ConsoleRequest();
        $result = $this->controller->dispatch($request);

        $this->assertNotNull($result);
    }

    public function testDispatchIncorrectRequest()
    {
        $this->setExpectedException('\Zend\Mvc\Exception\InvalidArgumentException');

        $request = new Request();
        $this->controller->dispatch($request);
    }

    public function testGetNoInjectedConsole()
    {
        $console = $this->controller->getConsole();

        $this->assertNull($console);
    }

    public function testGetInjectedConsole()
    {
        $consoleAdapter = $this->getMock('\Zend\Console\Adapter\AdapterInterface');

        $controller = $this->controller->setConsole($consoleAdapter);
        $console = $this->controller->getConsole();

        $this->assertInstanceOf('\Zend\Mvc\Controller\AbstractConsoleController', $controller);
        $this->assertInstanceOf('\Zend\Console\Adapter\AdapterInterface', $console);
    }
}
