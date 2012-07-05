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
 * @package    Zend_ModuleManager
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->setExpectedException('Zend\ModuleManager\Exception\InvalidArgumentException');
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
        $this->setExpectedException('Zend\ModuleManager\Exception\InvalidArgumentException');
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
}
