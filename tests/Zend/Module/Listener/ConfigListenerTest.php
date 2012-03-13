<?php

namespace ZendTest\Module\Listener;

use ArrayObject,
    InvalidArgumentException,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\AutoloaderFactory,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\Listener\ConfigListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Manager;

class ConfigListenerTest extends TestCase
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
            dirname(__DIR__) . '/TestAsset',
        ));
        $autoloader->register();

        $this->moduleManager = new Manager(array());
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
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

    public function testMultipleConfigsAreMerged()
    {
        $configListener = new ConfigListener;

        $moduleManager = $this->moduleManager;
        $moduleManager->events()->attach('loadModule', $configListener);
        $moduleManager->setModules(array('SomeModule', 'ListenerTestModule'));
        $moduleManager->loadModules();

        $config = $configListener->getMergedConfig(false);
        $this->assertSame(2, count($config));
        $this->assertSame('test', $config['listener']);
        $this->assertSame('thing', $config['some']);
        $configObject = $configListener->getMergedConfig();
        $this->assertInstanceOf('Zend\Config\Config', $configObject);
    }

    public function testCanCacheMergedConfig()
    {
        $options = new ListenerOptions(array(
            'cache_dir'            => $this->tmpdir,
            'config_cache_enabled' => true,
        ));
        $configListener = new ConfigListener($options);

        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('SomeModule', 'ListenerTestModule'));
        $moduleManager->events()->attach('loadModule', $configListener);
        $moduleManager->loadModules(); // This should cache the config

        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->getConfigCalled);

        // Now we check to make sure it uses the config and doesn't hit 
        // the module objects getConfig() method(s)
        $moduleManager = new Manager(array('SomeModule', 'ListenerTestModule'));
        $moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $configListener = new ConfigListener($options);
        $moduleManager->events()->attach('loadModule', $configListener);
        $moduleManager->loadModules();
        $modules = $moduleManager->getLoadedModules();
        $this->assertFalse($modules['ListenerTestModule']->getConfigCalled);
    }

    public function testBadConfigValueThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $configListener = new ConfigListener;

        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('BadConfigModule', 'SomeModule'));
        $moduleManager->events()->attach('loadModule', $configListener);
        $moduleManager->loadModules();
    }
    
    public function testBadGlobPathTrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $configListener = new ConfigListener;
        $configListener->addConfigGlobPath(array('asd'));
    }

    public function testBadGlobPathArrayTrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $configListener = new ConfigListener;
        $configListener->addConfigGlobPaths('asd');
    }

    public function testCanMergeConfigFromGlob()
    {
        $configListener = new ConfigListener;
        $configListener->addConfigGlobPath(__DIR__ . '/_files/good/*.{ini,php,xml}');

        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('SomeModule'));

        $moduleManager->events()->attachAggregate($configListener);

        $moduleManager->loadModules();
        $configObjectCheck = $configListener->getMergedConfig();

        // Test as object
        $configObject = $configListener->getMergedConfig();
        $this->assertSame(spl_object_hash($configObjectCheck), spl_object_hash($configObject));
        $this->assertSame('loaded', $configObject->ini);
        $this->assertSame('loaded', $configObject->php);
        $this->assertSame('loaded', $configObject->xml);
        // Test as array
        $config = $configListener->getMergedConfig(false);
        $this->assertSame('loaded', $config['ini']);
        $this->assertSame('loaded', $config['php']);
        $this->assertSame('loaded', $config['xml']);
    }

    public function testCanCacheMergedConfigFromGlob()
    {
        $options = new ListenerOptions(array(
            'cache_dir'            => $this->tmpdir,
            'config_cache_enabled' => true,
        ));
        $configListener = new ConfigListener($options);
        $configListener->addConfigGlobPath(__DIR__ . '/_files/good/*.{ini,php,xml}');

        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('SomeModule'));

        $moduleManager->events()->attachAggregate($configListener);

        $moduleManager->loadModules();
        $configObjectFromGlob = $configListener->getMergedConfig();

        // This time, don't add the glob path
        $configListener = new ConfigListener($options);
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);

        $moduleManager->events()->attachAggregate($configListener);

        $moduleManager->loadModules();

        // Check if values from glob object and cache object are the same
        $configObjectFromCache = $configListener->getMergedConfig();
        $this->assertNotNull($configObjectFromGlob->ini);
        $this->assertSame($configObjectFromGlob->ini, $configObjectFromCache->ini);
        $this->assertNotNull($configObjectFromGlob->php);
        $this->assertSame($configObjectFromGlob->php, $configObjectFromCache->php);
        $this->assertNotNull($configObjectFromGlob->xml);
        $this->assertSame($configObjectFromGlob->xml, $configObjectFromCache->xml);
    }

    public function testCanMergeConfigFromArrayOfGlobs()
    {
        $configListener = new ConfigListener;
        $configListener->addConfigGlobPaths(new ArrayObject(array(
            __DIR__ . '/_files/good/*.ini',
            __DIR__ . '/_files/good/*.php',
            __DIR__ . '/_files/good/*.xml',
        )));

        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('SomeModule'));

        $moduleManager->events()->attachAggregate($configListener);
        $moduleManager->loadModules();

        // Test as object
        $configObject = $configListener->getMergedConfig();
        $this->assertSame('loaded', $configObject->ini);
        $this->assertSame('loaded', $configObject->php);
        $this->assertSame('loaded', $configObject->xml);
    }

    public function testConfigListenerFunctionsAsAggregateListener()
    {
        $configListener = new ConfigListener;

        $moduleManager = $this->moduleManager;
        $this->assertEquals(1, count($moduleManager->events()->getEvents()));

        $configListener->attach($moduleManager->events());
        $this->assertEquals(4, count($moduleManager->events()->getEvents()));

        $configListener->detach($moduleManager->events());
        $this->assertEquals(1, count($moduleManager->events()->getEvents()));
    }
}
