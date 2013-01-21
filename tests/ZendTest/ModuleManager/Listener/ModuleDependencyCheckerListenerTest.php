<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ModuleManager\Listener\ModuleDependencyCheckerListener;
use Zend\ModuleManager\ModuleManager;

class ModuleDependencyCheckerListenerTest extends TestCase
{
    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testCallsGetModuleDependenciesOnModuleImplementingInterface()
    {
        //$moduleManager = new ModuleManager(array());
        //$moduleManager->getEventManager()->attach('loadModule', new ModuleDependencyCheckerListener(), 2000);

        $module = $this->getMock('Zend\ModuleManager\Feature\DependencyIndicatorInterface');
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue(array()));

        $event = $this->getMock('Zend\ModuleManager\ModuleEvent');
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $listener->__invoke($event);
    }

    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testCallsGetModuleDependenciesOnModuleNotImplementingInterface()
    {
        $module = $this->getMock('stdClass', array('getModuleDependencies'));
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue(array()));

        $event = $this->getMock('Zend\ModuleManager\ModuleEvent');
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $listener->__invoke($event);
    }

    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testNotFulfilledDependencyThrowsException()
    {
        $module = $this->getMock('stdClass', array('getModuleDependencies'));
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue(array('OtherModule')));

        $event = $this->getMock('Zend\ModuleManager\ModuleEvent');
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $this->setExpectedException('Zend\ModuleManager\Exception\MissingDependencyModuleException');
        $listener->__invoke($event);
    }
}
