<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Loader\AutoloaderFactory,
    Zend\Module\Manager,
    Zend\Module\Listener\ListenerOptions,
    Zend\EventManager\EventManager,
    Zend\Module\Listener\DefaultListenerAggregate,
    InvalidArgumentException;

class DefaultListenerAggregateTest extends TestCase
{
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

    public function testDefaultListenerAggregateCanAttachItself()
    {
        $moduleManager = new Manager(array('ListenerTestModule'));
        $moduleManager->events()->attachAggregate(new DefaultListenerAggregate);

        $events = $moduleManager->events()->getEvents();
        $expectedEvents = array(
            'loadModules.pre' => array(
                'Zend\Loader\ModuleAutoloader',
                'Zend\Module\Listener\ConfigListener',
            ),
            'loadModule.resolve' => array(
                'Zend\Module\Listener\ModuleResolverListener',
            ),
            'loadModule' => array(
                'Zend\Module\Listener\AutoloaderListener',
                'Zend\Module\Listener\InitTrigger',
                'Zend\Module\Listener\ConfigListener',
                'Zend\Module\Listener\LocatorRegistrationListener',
            ),
            'loadModules.post' => array(
                'Zend\Module\Listener\ConfigListener',
                'Zend\Module\Listener\LocatorRegistrationListener',
            ),
        );
        foreach ($expectedEvents as $event => $expectedListeners) {
            $this->assertContains($event, $events);
            $listeners = $moduleManager->events()->getListeners($event);
            $this->assertSame(count($expectedListeners), count($listeners));
            foreach ($listeners as $listener) {
                $callback = $listener->getCallback();
                if (is_array($callback)) {
                    $callback = $callback[0];
                }
                $listenerClass = get_class($callback);
                $this->assertContains($listenerClass, $expectedListeners);
            }
        }
    }

    public function testDefaultListenerAggregateCanDetachItself()
    {
        $listenerAggregate = new DefaultListenerAggregate;
        $moduleManager = new Manager(array('ListenerTestModule'));

        $listenerAggregate->attach($moduleManager->events());
        $this->assertEquals(4, count($moduleManager->events()->getEvents()));

        $listenerAggregate->detach($moduleManager->events());
        $this->assertEquals(0, count($moduleManager->events()->getEvents()));
    }
}
