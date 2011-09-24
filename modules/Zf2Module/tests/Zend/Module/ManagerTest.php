<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\Manager,
    Zend\Module\ManagerOptions,
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
            __DIR__ . '/../Loader/TestAsset',
        ));
        $autoloader->register();
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

    public function testDefaultManagerOptions()
    {
        $moduleManager = new Manager(array());
        $this->assertInstanceOf('Zend\Module\ManagerOptions', $moduleManager->getOptions());
    }

    public function testCanSetManagerOptionsInConstructor()
    {
        $options = new ManagerOptions(array('cache_dir' => __DIR__));
        $moduleManager = new Manager(array(), $options);
        $this->assertSame(__DIR__, $moduleManager->getOptions()->cache_dir);
    }

    public function testCanLoadFooModule()
    {
        $moduleManager = new Manager(array('FooModule'));
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('FooModule\Module', $loadedModules['FooModule']);
    }

    public function testCanLoadMultipleModules()
    {
        $moduleManager = new Manager(array('BarModule', 'BazModule'));
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('BarModule\Module', $loadedModules['BarModule']);
        $this->assertInstanceOf('BazModule\Module', $loadedModules['BazModule']);
        $config = $moduleManager->getMergedConfig();
        $this->assertSame('foo', $config->bar);
        $this->assertSame('bar', $config->baz);
    }

    public function testCanCacheMerchedConfig()
    {
        $options = new ManagerOptions(array(
            'cache_config' => true,
            'cache_dir' => $this->tmpdir,
        ));
        // build the cache
        $moduleManager = new Manager(array('BarModule', 'BazModule'), $options);
        $config = $moduleManager->getMergedConfig();
        $this->assertSame('foo', $config->bar);
        $this->assertSame('bar', $config->baz);

        // use the cache
        $moduleManager = new Manager(array('BarModule', 'BazModule'), $options);
        $config = $moduleManager->getMergedConfig();
        $this->assertSame('foo', $config->bar);
        $this->assertSame('bar', $config->baz);
    }

    public function testConstructorThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $moduleManager = new Manager('foo');
    }
}
