<?php

namespace ZendTest\Module\Listener;

use ArrayObject,
    InvalidArgumentException,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\AutoloaderFactory,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\Listener\ConfigListener,
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
    }

    public function tearDown()
    {
        $file = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        //@unlink($file[0]); // change this if there's ever > 1 file 
        //@rmdir($this->tmpdir);
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
        $moduleManager = new Manager(array('SomeModule', 'ListenerTestModule'));
        $moduleManager->loadModules();
        $config = $moduleManager->getConfigListener()->getMergedConfig(false);
        $this->assertSame(2, count($config));
        $this->assertSame('test', $config['listener']);
        $this->assertSame('thing', $config['some']);
        $configObject = $moduleManager->getConfigListener()->getMergedConfig();
        $this->assertInstanceOf('Zend\Config\Config', $configObject);
    }

    public function testCanCacheMergedConfig()
    {
        $moduleManager = new Manager(array('SomeModule', 'ListenerTestModule'));
        $options = new ListenerOptions(array(
            'cache_dir'            => $this->tmpdir,
            'config_cache_enabled' => true,
        ));
        $moduleManager->setDefaultListenerOptions($options);
        $moduleManager->loadModules(); // This should cache the config
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->getConfigCalled);

        // Now we check to make sure it uses the config and doesn't hit 
        // the module objects getConfig() method(s)
        $moduleManager = new Manager(array('SomeModule', 'ListenerTestModule'));
        $moduleManager->setDefaultListenerOptions($options);
        $moduleManager->loadModules();
        $modules = $moduleManager->getLoadedModules();
        $this->assertFalse($modules['ListenerTestModule']->getConfigCalled);
    }

    public function testBadConfigValueThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $moduleManager = new Manager(array('BadConfigModule', 'SomeModule'));
        $moduleManager->loadModules();
    }
    
    public function testBadConfigFileExtensionThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->getConfigListener()->addConfigGlobPath(__DIR__ . '/_files/bad/*.badext');
        $moduleManager->loadModules();
    }

    public function testBadGlobPathTrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->getConfigListener()->addConfigGlobPath(array('asd'));
        $moduleManager->loadModules();
    }

    public function testBadGlobPathArrayTrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->getConfigListener()->addConfigGlobPaths('asd');
        $moduleManager->loadModules();
    }

    public function testCanMergeConfigFromGlob()
    {
        $moduleManager = new Manager(array('SomeModule'));
        $options = new ListenerOptions;
        $moduleManager->setDefaultListenerOptions($options);
        $moduleManager->getConfigListener()->addConfigGlobPath(__DIR__ . '/_files/good/*.{ini,json,php,xml,yml}');
        $moduleManager->loadModules();
        $moduleManager->getMergedConfig(); 
        // Test as object
        $configObject = $moduleManager->getMergedConfig();
        $this->assertSame('loaded', $configObject->ini);
        $this->assertSame('loaded', $configObject->php);
        $this->assertSame('loaded', $configObject->json);
        $this->assertSame('loaded', $configObject->xml);
        $this->assertSame('loaded', $configObject->yml);
        // Test as array
        $config = $moduleManager->getMergedConfig(false);
        $this->assertSame('loaded', $config['ini']);
        $this->assertSame('loaded', $config['json']);
        $this->assertSame('loaded', $config['php']);
        $this->assertSame('loaded', $config['xml']);
        $this->assertSame('loaded', $config['yml']);
    }

    public function testCanMergeConfigFromArrayOfGlobs()
    {
        $moduleManager = new Manager(array('SomeModule'));
        $options = new ListenerOptions;
        $moduleManager->setDefaultListenerOptions($options);
        $moduleManager->getConfigListener()->addConfigGlobPaths(new ArrayObject(array(
            __DIR__ . '/_files/good/*.ini',
            __DIR__ . '/_files/good/*.json',
            __DIR__ . '/_files/good/*.php',
            __DIR__ . '/_files/good/*.xml',
            __DIR__ . '/_files/good/*.yml',
        )));
        $moduleManager->loadModules();
        // Test as object
        $configObject = $moduleManager->getMergedConfig();
        $this->assertSame('loaded', $configObject->ini);
        $this->assertSame('loaded', $configObject->php);
        $this->assertSame('loaded', $configObject->json);
        $this->assertSame('loaded', $configObject->xml);
        $this->assertSame('loaded', $configObject->yml);
    }

    public function testPhpConfigFileReturningInvalidConfigRaisesException()
    {
        $moduleManager  = new Manager(array('SomeModule'));
        $configListener = $moduleManager->getConfigListener();
        $configListener->addConfigGlobPaths(new ArrayObject(array(
            __DIR__ . '/_files/bad/*.php',
        )));
        $this->setExpectedException('Zend\Module\Listener\Exception\RuntimeException', 'Invalid configuration');
        $moduleManager->loadModules();
    }
}
