<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Module\ModuleEvent;

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
}
