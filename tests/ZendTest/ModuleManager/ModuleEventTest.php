<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\ModuleManager\Listener\ConfigListener;
use Zend\ModuleManager\ModuleEvent;

class ModuleEventTest extends TestCase
{
    public function setUp()
    {
        $this->event = new ModuleEvent();
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
        $this->setExpectedException('Zend\ModuleManager\Exception\InvalidArgumentException');
        $this->event->setModule('foo');
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
        $this->setExpectedException('Zend\ModuleManager\Exception\InvalidArgumentException');
        $this->event->setModuleName(new stdClass);
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
}
