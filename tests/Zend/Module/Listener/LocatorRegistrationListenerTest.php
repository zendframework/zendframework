<?php

namespace ZendTest\Module\Listener;

use ArrayObject,
    InvalidArgumentException,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\AutoloaderFactory,
    Zend\Loader\ModuleAutoloader,
    Zend\Mvc\Bootstrap,
    Zend\Mvc\Application,
    Zend\Config\Config,
    Zend\Module\Listener\LocatorRegistrationListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Manager;

class LocatorRegistrationTest extends TestCase
{
    public $module;

    public function setUp()
    {
        // Store original autoloaders
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

        $this->moduleManager = new Manager(array('ListenerTestModule'));
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
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

    public function testModuleClassIsRegisteredWithDiAndInjectedWithSharedInstances()
    {
        $locatorRegistrationListener = new LocatorRegistrationListener;
        $this->moduleManager->events()->attachAggregate($locatorRegistrationListener);
        $test = $this;
        $this->moduleManager->events()->attach('loadModule', function ($e) use ($test) {
            $test->module = $e->getModule();
        }, -1000);
        $this->moduleManager->loadModules();

        $bootstrap       = new Bootstrap(new Config(array('di' => array())));
        $application     = new Application;
        $bootstrap->bootstrap($application);
        $locator         = $application->getLocator();
        $sharedInstance1 = $locator->instanceManager()->getSharedInstance('ListenerTestModule\Module');
        $sharedInstance2 = $locator->instanceManager()->getSharedInstance('Zend\Module\Manager');

        $this->assertInstanceOf('ListenerTestModule\Module', $sharedInstance1);
        $this->assertSame($this->module, $locator->get('Foo\Bar')->module);

        $this->assertInstanceOf('Zend\Module\Manager', $sharedInstance2);
        $this->assertSame($this->moduleManager, $locator->get('Foo\Bar')->moduleManager);
    }
}
