<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\Manager,
    Zend\Module\Listener\ConfigListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Loader\AutoloaderFactory,
    InvalidArgumentException;

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
        $moduleManager->getConfigListener()->addConfigGlobPath(dirname(__DIR__) . '/_files/*.{bad}');
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
        $moduleManager->getConfigListener()->addConfigGlobPath(dirname(__DIR__) . '/_files/*.{ini,json,php,xml,yaml}');
        $moduleManager->loadModules();
        $moduleManager->getMergedConfig(); 
        // Test as object
        $configObject = $moduleManager->getMergedConfig()->all;
        $this->assertSame('yes', $configObject->ini);
        $this->assertSame('yes', $configObject->php);
        $this->assertSame('yes', $configObject->json);
        $this->assertSame('yes', $configObject->xml);
        $this->assertTrue($configObject->yaml);
        // Test as array
        $config = $moduleManager->getMergedConfig(false);
        $this->assertSame('yes', $config['all']['ini']);
        $this->assertSame('yes', $config['all']['json']);
        $this->assertSame('yes', $config['all']['php']);
        $this->assertSame('yes', $config['all']['xml']);
        $this->assertTrue($config['all']['yaml']); // stupid yaml
    }

    public function testCanMergeConfigFromArrayOfGlobs()
    {
        $moduleManager = new Manager(array('SomeModule'));
        $moduleManager->getConfigListener()->addConfigGlobPaths(new \ArrayObject(array(
            dirname(__DIR__) . '/_files/*.ini',
            dirname(__DIR__) . '/_files/*.json',
            dirname(__DIR__) . '/_files/*.php',
            dirname(__DIR__) . '/_files/*.xml',
            dirname(__DIR__) . '/_files/*.yaml',
        )));
        $moduleManager->loadModules();
        // Test as object
        $configObject = $moduleManager->getMergedConfig()->all;
        $this->assertSame('yes', $configObject->ini);
        $this->assertSame('yes', $configObject->php);
        $this->assertSame('yes', $configObject->json);
        $this->assertSame('yes', $configObject->xml);
        $this->assertTrue($configObject->yaml);
    }
}
