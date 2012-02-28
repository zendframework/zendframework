<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Loader\AutoloaderFactory,
    Zend\Module\Manager,
    Zend\Module\Listener\ListenerOptions,
    Zend\EventManager\EventManager,
    Zend\Module\Listener\DefaultListenerAggregate,
    InvalidArgumentException;

class ManagerTest extends TestCase
{
    public function setUp()
    {
        $this->tmpdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'zend_module_cache_dir';
        @mkdir($this->tmpdir);
        $this->configCache = $this->tmpdir . DIRECTORY_SEPARATOR . 'config.cache.php';
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        $this->defaultListeners = new DefaultListenerAggregate(
            new ListenerOptions(array( 
                'module_paths'         => array(
                    realpath(__DIR__ . '/TestAsset'),
                ),
            ))
        );
    }

    public function tearDown()
    {
        $file = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        @unlink($file[0]); // change this if there's ever > 1 file 
        @rmdir($this->tmpdir);
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

    public function testCanLoadSomeModule()
    {
        $configListener = $this->defaultListeners->getConfigListener();
        $moduleManager  = new Manager(array('SomeModule'), new EventManager);
        $moduleManager->events()->attachAggregate($this->defaultListeners);
        $moduleManager->loadModules();
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('SomeModule\Module', $loadedModules['SomeModule']);
        $config = $configListener->getMergedConfig();
        $this->assertSame($config->some, 'thing');
    }

    public function testCanLoadMultipleModules()
    {
        $configListener = $this->defaultListeners->getConfigListener();
        $moduleManager  = new Manager(array('BarModule', 'BazModule'));
        $moduleManager->events()->attachAggregate($this->defaultListeners);
        $moduleManager->loadModules();
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('BarModule\Module', $loadedModules['BarModule']);
        $this->assertInstanceOf('BazModule\Module', $loadedModules['BazModule']);
        $this->assertInstanceOf('BarModule\Module', $moduleManager->getModule('BarModule'));
        $this->assertInstanceOf('BazModule\Module', $moduleManager->getModule('BazModule'));
        $this->assertNull($moduleManager->getModule('NotLoaded'));
        $config = $configListener->getMergedConfig();
        $this->assertSame('foo', $config->bar);
        $this->assertSame('bar', $config->baz);
    }

    public function testModuleLoadingBehavior()
    {
        $moduleManager = new Manager(array('BarModule'));
        $moduleManager->events()->attachAggregate($this->defaultListeners);
        $modules = $moduleManager->getLoadedModules();
        $this->assertSame(0, count($modules));
        $modules = $moduleManager->getLoadedModules(true);
        $this->assertSame(1, count($modules));
        $moduleManager->loadModules(); // should not cause any problems
        $moduleManager->loadModule('BarModule'); // should not cause any problems
        $modules = $moduleManager->getLoadedModules(true); // BarModule already loaded so nothing happens
        $this->assertSame(1, count($modules));
    }

    public function testConstructorThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $moduleManager = new Manager('stringShouldBeArray');
    }

    public function testNotFoundModuleThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $moduleManager = new Manager(array('NotFoundModule'));
        $moduleManager->loadModules();
    }
}
