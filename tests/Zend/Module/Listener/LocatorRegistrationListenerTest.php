<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\EventManager,
    Zend\EventManager\SharedEventManager,
    Zend\Loader\AutoloaderFactory,
    Zend\Loader\ModuleAutoloader,
    Zend\Mvc\Application,
    Zend\Module\Listener\LocatorRegistrationListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Manager,
    Zend\ServiceManager\ServiceManager,
    ZendTest\Module\TestAsset\MockApplication;

require_once dirname(__DIR__) . '/TestAsset/ListenerTestModule/src/Foo/Bar.php';

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

        $this->sharedEvents = new SharedEventManager();

        $this->moduleManager = new Manager(array('ListenerTestModule'));
        $this->moduleManager->events()->setSharedManager($this->sharedEvents);
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);

        $this->application = new MockApplication;
        $events            = new EventManager(array('Zend\Mvc\Application', 'ZendTest\Module\TestAsset\MockApplication', 'application'));
        $events->setSharedManager($this->sharedEvents);
        $this->application->setEventManager($events);

        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setService('ModuleManager', $this->moduleManager);
        $this->application->setServiceManager($this->serviceManager);
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
        $locator         = $this->serviceManager;
        $locator->setFactory('Foo\Bar', function($s) {
            $module   = $s->get('ListenerTestModule\Module');
            $manager  = $s->get('Zend\Module\Manager');
            $instance = new \Foo\Bar($module, $manager);
            return $instance;
        });

        $locatorRegistrationListener = new LocatorRegistrationListener;
        $this->moduleManager->events()->attachAggregate($locatorRegistrationListener);
        $test = $this;
        $this->moduleManager->events()->attach('loadModule', function ($e) use ($test) {
            $test->module = $e->getModule();
        }, -1000);
        $this->moduleManager->loadModules();

        $this->application->bootstrap();
        $sharedInstance1 = $locator->get('ListenerTestModule\Module');
        $sharedInstance2 = $locator->get('Zend\Module\Manager');

        $this->assertInstanceOf('ListenerTestModule\Module', $sharedInstance1);
        $this->assertSame($this->module, $locator->get('Foo\Bar')->module);

        $this->assertInstanceOf('Zend\Module\Manager', $sharedInstance2);
        $this->assertSame($this->moduleManager, $locator->get('Foo\Bar')->moduleManager);
    }
}
