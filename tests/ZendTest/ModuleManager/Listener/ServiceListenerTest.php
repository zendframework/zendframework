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

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\ModuleManager\Listener\ConfigListener;
use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager;

class ServiceListenerTest extends TestCase
{
    protected $serviceManagerProps = array(
        'invokableClasses',
        'factories',
        'abstractFactories',
        'shared',
        'instances',
        'aliases',
        'initializers',
        'peeringServiceManagers',
    );

    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->listener = new ServiceListener($this->services);
        $this->listener->addServiceManager($this->services, 'service_manager', 'Zend\ModuleManager\Feature\ServiceProviderInterface', 'getServiceConfig');
        $this->event    = new ModuleEvent();
        $this->configListener = new ConfigListener();
        $this->event->setConfigListener($this->configListener);
    }

    public function testPassingInvalidModuleDoesNothing()
    {
        $module = new stdClass();
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);

        foreach ($this->serviceManagerProps as $prop) {
            $this->assertAttributeEquals(array(), $prop, $this->services);
        }
    }

    public function testInvalidReturnFromModuleDoesNothing()
    {
        $module = new TestAsset\ServiceInvalidReturnModule();
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);

        foreach ($this->serviceManagerProps as $prop) {
            $this->assertAttributeEquals(array(), $prop, $this->services);
        }
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(__CLASS__ => __CLASS__),
            'factories' => array(
                'foo' => function ($sm) { },
            ),
            'abstract_factories' => array(
                new \Zend\ServiceManager\Di\DiAbstractServiceFactory(new \Zend\Di\Di()),
            ),
            'shared' => array(
                'foo' => false,
                'zendtestmodulemanagerlistenerservicelistenertest' => true,
            ),
            'aliases'  => array(
                'bar' => 'foo',
            ),
        );
    }

    public function assertServiceManagerIsConfigured()
    {
        $this->listener->onLoadModulesPost($this->event);
        foreach ($this->getServiceConfig() as $prop => $expected) {
            if ($prop == 'invokables') {
                $prop = 'invokableClasses';
                foreach ($expected as $key => $value) {
                    $normalized = strtolower($key);
                    $normalized = str_replace(array('\\', '_'), '', $normalized);
                    unset($expected[$key]);
                    $expected[$normalized] = $value;
                }
            }
            if ($prop == 'abstract_factories') {
                $prop = 'abstractFactories';
            }
            $this->assertAttributeEquals($expected, $prop, $this->services, "$prop assertion failed");
        }
    }

    public function testModuleReturningArrayConfiguresServiceManager()
    {
        $config = $this->getServiceConfig();
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testModuleReturningTraversableConfiguresServiceManager()
    {
        $config = $this->getServiceConfig();
        $config = new ArrayObject($config);
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testModuleServiceConfigOverridesGlobalConfig()
    {
        $this->listener = new ServiceListener($this->services, array('aliases' => array('foo' => 'bar')));
        $this->listener->addServiceManager($this->services, 'service_manager', 'Zend\ModuleManager\Feature\ServiceProviderInterface', 'getServiceConfig');
        $config = array('aliases' => array('foo' => 'baz'));
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->listener->onLoadModulesPost($this->event);
        $this->assertAttributeEquals($config['aliases'], 'aliases', $this->services, "aliases assertion failed - module config did not override main config");
    }

    public function testModuleReturningServiceConfigConfiguresServiceManager()
    {
        $config = $this->getServiceConfig();
        $config = new ServiceConfig($config);
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testMergedConfigContainingServiceManagerKeyWillConfigureServiceManagerPostLoadModules()
    {
        $config = array('service_manager' => $this->getServiceConfig());
        $configListener = new ConfigListener();
        $configListener->setMergedConfig($config);
        $this->event->setConfigListener($configListener);
        $this->assertServiceManagerIsConfigured();
    }
}
