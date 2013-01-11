<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\EventManager\EventManager;
use Zend\Loader\AutoloaderFactory;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use InvalidArgumentException;

class ModuleManagerTest extends TestCase
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

    public function testEventManagerIdentifiers()
    {
        $moduleManager = new ModuleManager(array());
        $identifiers = $moduleManager->getEventManager()->getIdentifiers();
        $expected    = array('Zend\ModuleManager\ModuleManager', 'module_manager');
        $this->assertEquals($expected, array_values($identifiers));
    }

    public function testCanLoadSomeModule()
    {
        $configListener = $this->defaultListeners->getConfigListener();
        $moduleManager  = new ModuleManager(array('SomeModule'), new EventManager);
        $moduleManager->getEventManager()->attachAggregate($this->defaultListeners);
        $moduleManager->loadModules();
        $loadedModules = $moduleManager->getLoadedModules();
        $this->assertInstanceOf('SomeModule\Module', $loadedModules['SomeModule']);
        $config = $configListener->getMergedConfig();
        $this->assertSame($config->some, 'thing');
    }

    public function testCanLoadMultipleModules()
    {
        $configListener = $this->defaultListeners->getConfigListener();
        $moduleManager  = new ModuleManager(array('BarModule', 'BazModule'));
        $moduleManager->getEventManager()->attachAggregate($this->defaultListeners);
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
        $moduleManager = new ModuleManager(array('BarModule'));
        $moduleManager->getEventManager()->attachAggregate($this->defaultListeners);
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
        $moduleManager = new ModuleManager('stringShouldBeArray');
    }

    public function testNotFoundModuleThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $moduleManager = new ModuleManager(array('NotFoundModule'));
        $moduleManager->loadModules();
    }

    public function testCanLoadModuleDuringTheLoadModuleEvent()
    {
        $configListener = $this->defaultListeners->getConfigListener();
        $moduleManager  = new ModuleManager(array('LoadOtherModule', 'BarModule'));
        $moduleManager->getEventManager()->attachAggregate($this->defaultListeners);
        $moduleManager->loadModules();

        $config = $configListener->getMergedConfig();
        $this->assertTrue(isset($config['loaded']));
        $this->assertSame('oh, yeah baby!', $config['loaded']);
    }

    public function testModuleIsMarkedAsLoadedWhenLoadModuleEventIsTriggered()
    {
        $test          = new stdClass;
        $moduleManager = new ModuleManager(array('BarModule'));
        $events        = $moduleManager->getEventManager();
        $events->attachAggregate($this->defaultListeners);
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, function ($e) use ($test) {
            $test->modules = $e->getTarget()->getLoadedModules(false);
        });

        $moduleManager->loadModules();

        $this->assertTrue(isset($test->modules));
        $this->assertArrayHasKey('BarModule', $test->modules);
        $this->assertInstanceOf('BarModule\Module', $test->modules['BarModule']);
    }
}
