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

namespace ZendTest\ModuleManager\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Config;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\Listener\ModuleResolverListener;
use Zend\ModuleManager\Listener\OnBootstrapListener;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use ZendTest\ModuleManager\TestAsset\MockApplication;

class OnBootstrapListenerTest extends TestCase
{

    public function setUp()
    {
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        $autoloader = new ModuleAutoloader(array(
            dirname(__DIR__) . '/TestAsset',
        ));
        $autoloader->register();

        $sharedEvents = new SharedEventManager();
        $this->moduleManager = new ModuleManager(array());
        $this->moduleManager->getEventManager()->setSharedManager($sharedEvents);
        $this->moduleManager->getEventManager()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->moduleManager->getEventManager()->attach('loadModule', new OnBootstrapListener, 1000);

        $this->application = new MockApplication;
        $events            = new EventManager(array('Zend\Mvc\Application', 'ZendTest\Module\TestAsset\MockApplication', 'application'));
        $events->setSharedManager($sharedEvents);
        $this->application->setEventManager($events);
    }

    public function tearDown()
    {
        // Restore original autoloaders
        AutoloaderFactory::unregisterAutoloaders();
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testOnBootstrapMethodCalledByOnBootstrapListener()
    {
        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('ListenerTestModule'));
        $moduleManager->loadModules();
        $this->application->bootstrap();
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->onBootstrapCalled);
    }
}
