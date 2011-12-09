<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Module\ModuleEvent,
    Zend\Module\Listener\ConfigListener;

class ModuleEventTest extends TestCase
{
    public function setUp()
    {
        $this->event = new ModuleEvent();
    }

    public function testSettingModuleProxiesToParameters()
    {
        $module = new stdClass;
        $this->event->setModule($module);
        $test = $this->event->getParam('module');
        $this->assertSame($module, $test);
    }

    public function testCanRetrieveModuleViaGetter()
    {
        $module = new stdClass;
        $this->event->setModule($module);
        $test = $this->event->getModule();
        $this->assertSame($module, $test);
    }

    public function testPassingNonObjectToSetModuleRaisesException()
    {
        $this->setExpectedException('Zend\Module\Exception\InvalidArgumentException');
        $this->event->setModule('foo');
    }

    public function testSettingModuleNameProxiesToParameters()
    {
        $moduleName = 'MyModule';
        $this->event->setModuleName($moduleName);
        $test = $this->event->getParam('moduleName');
        $this->assertSame($moduleName, $test);
    }

    public function testCanRetrieveModuleNameViaGetter()
    {
        $moduleName = 'MyModule';
        $this->event->setModuleName($moduleName);
        $test = $this->event->getModuleName();
        $this->assertSame($moduleName, $test);
    }

    public function testPassingNonStringToSetModuleNameRaisesException()
    {
        $this->setExpectedException('Zend\Module\Exception\InvalidArgumentException');
        $this->event->setModuleName(new StdClass);
    }

    public function testSettingConfigListenerProxiesToParameters()
    {
        $configListener = new ConfigListener;
        $this->event->setConfigListener($configListener);
        $test = $this->event->getParam('configListener');
        $this->assertSame($configListener, $test);
    }

    public function testCanRetrieveConfigListenerViaGetter()
    {
        $configListener = new ConfigListener;
        $this->event->setConfigListener($configListener);
        $test = $this->event->getConfigListener();
        $this->assertSame($configListener, $test);
    }

    public function testPassingNonConfigMergerToSetConfigListenerRaisesException()
    {
        $this->setExpectedException('Zend\Module\Exception\InvalidArgumentException');
        $this->event->setConfigListener(new StdClass);
    }
}
