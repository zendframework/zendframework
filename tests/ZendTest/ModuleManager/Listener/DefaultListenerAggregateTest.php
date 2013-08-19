<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Loader\ModuleAutoloader;
use Zend\Loader\AutoloaderFactory;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\ModuleManager;

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
        $moduleManager = new ModuleManager(array('ListenerTestModule'));
        $moduleManager->getEventManager()->attachAggregate(new DefaultListenerAggregate);

        $events = $moduleManager->getEventManager()->getEvents();
        $expectedEvents = array(
            'loadModules' => array(
                'Zend\Loader\ModuleAutoloader',
                'config-pre' => 'Zend\ModuleManager\Listener\ConfigListener',
                'config-post' => 'Zend\ModuleManager\Listener\ConfigListener',
                'Zend\ModuleManager\Listener\LocatorRegistrationListener',
                'Zend\ModuleManager\ModuleManager',
            ),
            'loadModule.resolve' => array(
                'Zend\ModuleManager\Listener\ModuleResolverListener',
            ),
            'loadModule' => array(
                'Zend\ModuleManager\Listener\AutoloaderListener',
                'Zend\ModuleManager\Listener\ModuleDependencyCheckerListener',
                'Zend\ModuleManager\Listener\InitTrigger',
                'Zend\ModuleManager\Listener\OnBootstrapListener',
                'Zend\ModuleManager\Listener\ConfigListener',
                'Zend\ModuleManager\Listener\LocatorRegistrationListener',
            ),
        );
        foreach ($expectedEvents as $event => $expectedListeners) {
            $this->assertContains($event, $events);
            $listeners = $moduleManager->getEventManager()->getListeners($event);
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
        $moduleManager     = new ModuleManager(array('ListenerTestModule'));

        $this->assertEquals(1, count($moduleManager->getEventManager()->getEvents()));

        $listenerAggregate->attach($moduleManager->getEventManager());
        $this->assertEquals(3, count($moduleManager->getEventManager()->getEvents()));

        $listenerAggregate->detach($moduleManager->getEventManager());
        $this->assertEquals(1, count($moduleManager->getEventManager()->getEvents()));
    }
}
