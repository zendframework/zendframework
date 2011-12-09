<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Loader\AutoloaderFactory,
    Zend\Module\Listener\AutoloaderListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Manager;

class AutoloaderListenerTest extends TestCase
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

        $this->moduleManager = new Manager(array());
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->moduleManager->events()->attach('loadModule', new AutoloaderListener, 2000);
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

    public function testAutoloadersRegisteredByAutoloaderListener()
    {
        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('ListenerTestModule'));
        $moduleManager->loadModules();
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->getAutoloaderConfigCalled);
        $this->assertTrue(class_exists('Foo\Bar'));
    }

    public function testAutoloadersNotRegisteredIfModuleDoesNotInheritAutoloaderProviderInterface()
    {
        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('NotAutoloaderModule'));
        $moduleManager->loadModules();
        $modules = $moduleManager->getLoadedModules();
        $this->assertFalse($modules['NotAutoloaderModule']->getAutoloaderConfigCalled);
        $this->assertFalse(class_exists('Foo\Baz'));
    }
}
