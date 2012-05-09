<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Loader\AutoloaderFactory,
    Zend\Mvc\MvcEvent,
    Zend\Module\Listener\OnBootstrapListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Manager,
    Zend\Mvc\Application,
    Zend\Config\Config,
    Zend\EventManager\SharedEventManager,
    Zend\Mvc\Bootstrap;

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
        $this->bootstrap   = new Bootstrap(new Config(array('di' => array())));
        $this->bootstrap->events()->setSharedCollections($sharedEvents);
        $this->moduleManager = new Manager(array());
        $this->moduleManager->events()->setSharedCollections($sharedEvents);
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->moduleManager->events()->attach('loadModule', new OnBootstrapListener, 1000);
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
        $this->bootstrap->bootstrap(new Application);
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->onBootstrapCalled);
    }
}
