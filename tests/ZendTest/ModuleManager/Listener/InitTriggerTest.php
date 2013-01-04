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
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\Listener\InitTrigger;
use Zend\ModuleManager\Listener\ModuleResolverListener;
use Zend\ModuleManager\ModuleManager;

class InitTriggerTest extends TestCase
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

        $this->moduleManager = new ModuleManager(array());
        $this->moduleManager->getEventManager()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->moduleManager->getEventManager()->attach('loadModule', new InitTrigger, 2000);
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

    public function testInitMethodCalledByInitTriggerListener()
    {
        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('ListenerTestModule'));
        $moduleManager->loadModules();
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->initCalled);
    }
}
