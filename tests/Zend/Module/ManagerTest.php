<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\Manager,
    Zend\Module\Listener\ListenerOptions,
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

        $autoloader = new ModuleAutoloader(array(
            __DIR__ . '/TestAsset',
        ));
        $autoloader->register();
        \AutoInstallModule\Module::$RESPONSE = true;
        \AutoInstallModule\Module::$VERSION = '1.0.0';
    }

    public function tearDown()
    {
        $file = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        @unlink($file[0]); // change this if there's ever > 1 file 
        @rmdir($this->tmpdir);
        // Restore original autoloaders
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

    public function testDefaultListenerOptions()
    {
        $moduleManager = new Manager(array());
        $this->assertInstanceOf('Zend\Module\Listener\ListenerOptions', $moduleManager->getDefaultListenerOptions());
    }

    public function testCanSetDefaultListenerOptions()
    {
        $options = new ListenerOptions(array('cache_dir' => __DIR__));
        $moduleManager = new Manager(array());
        $moduleManager->setDefaultListenerOptions($options);
        $this->assertSame(__DIR__, $moduleManager->getDefaultListenerOptions()->cache_dir);
    }

    public function testCanLoadSomeModule()
    {
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->loadModules();
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('SomeModule\Module', $loadedModules['SomeModule']);
        $config = $moduleManager->getConfigListener()->getMergedConfig();
        $this->assertSame($config->some, 'thing');
    }

    public function testCanLoadMultipleModules()
    {
        $moduleManager = new Manager(array('BarModule', 'BazModule'));
        $moduleManager->loadModules();
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('BarModule\Module', $loadedModules['BarModule']);
        $this->assertInstanceOf('BazModule\Module', $loadedModules['BazModule']);
        $config = $moduleManager->getConfigListener()->getMergedConfig();
        $this->assertSame('foo', $config->bar);
        $this->assertSame('bar', $config->baz);
    }

    public function testDefaultModuleListenersAreLoaded()
    {
        $moduleManager = new Manager(array());
        $listeners = $moduleManager->events()->getListeners('loadModule');
        $this->assertSame(3, count($listeners));
    }

    public function testCanSkipDefaultModuleListeners()
    {
        $moduleManager = new Manager(array());
        $moduleManager->setDisableLoadDefaultListeners(true);
        $listeners = $moduleManager->events()->getListeners('loadModule');
        $this->assertSame(0, count($listeners));
    }

    public function testModuleLoadingBehavior()
    {
        $moduleManager = new Manager(array('BarModule'));
        $modules = $moduleManager->getLoadedModules();
        $this->assertSame(0, count($modules));
        $modules = $moduleManager->getLoadedModules(true);
        $this->assertSame(1, count($modules));
        $moduleManager->loadModules(); // should not cause any problems
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
