<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\ServiceManager\ServiceManager;

class ControllerManagerTest extends TestCase
{
    public function setUp()
    {
        $this->events       = new EventManager();
        $this->sharedEvents = new SharedEventManager;
        $this->events->setSharedManager($this->sharedEvents);

        $this->plugins  = new ControllerPluginManager();
        $this->services = new ServiceManager();
        $this->services->setService('Zend\ServiceManager\ServiceLocatorInterface', $this->services);
        $this->services->setService('EventManager', $this->events);
        $this->services->setService('SharedEventManager', $this->sharedEvents);
        $this->services->setService('ControllerPluginManager', $this->plugins);

        $this->controllers = new ControllerManager();
        $this->controllers->setServiceLocator($this->services);
    }

    public function testInjectControllerDependenciesInjectsExpectedDependencies()
    {
        $controller = new TestAsset\SampleController();
        $this->controllers->injectControllerDependencies($controller, $this->controllers);
        $this->assertSame($this->services, $controller->getServiceLocator());
        $this->assertSame($this->plugins, $controller->getPluginManager());

        // The default AbstractController implementation lazy instantiates an EM
        // instance, which means we need to check that that instance gets injected
        // with the shared EM instance.
        $events = $controller->getEventManager();
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $events);
        $this->assertSame($this->sharedEvents, $events->getSharedManager());
    }

    public function testInjectControllerDependenciesWillNotOverwriteExistingEventManager()
    {
        $events     = new EventManager();
        $controller = new TestAsset\SampleController();
        $controller->setEventManager($events);
        $this->controllers->injectControllerDependencies($controller, $this->controllers);
        $this->assertSame($events, $controller->getEventManager());
        $this->assertSame($this->sharedEvents, $events->getSharedManager());
    }
}
