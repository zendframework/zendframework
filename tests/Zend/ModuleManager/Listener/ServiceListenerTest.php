<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\ModuleManager\Listener;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\ModuleManager\Listener\ConfigListener;
use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\Configuration as ServiceConfiguration;
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
        $this->listener->addServiceManager($this->services, 'service_manager', 'Zend\ModuleManager\Feature\ServiceProviderInterface', 'getServiceConfiguration');
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

    public function getServiceConfiguration()
    {
        return array(
            'invokables' => array(__CLASS__ => __CLASS__),
            'factories' => array(
                'foo' => function($sm) { },
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
        foreach ($this->getServiceConfiguration() as $prop => $expected) {
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
        $config = $this->getServiceConfiguration();
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testModuleReturningTraversableConfiguresServiceManager()
    {
        $config = $this->getServiceConfiguration();
        $config = new ArrayObject($config);
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testModuleReturningServiceConfigurationConfiguresServiceManager()
    {
        $config = $this->getServiceConfiguration();
        $config = new ServiceConfiguration($config);
        $module = new TestAsset\ServiceProviderModule($config);
        $this->event->setModule($module);
        $this->listener->onLoadModule($this->event);
        $this->assertServiceManagerIsConfigured();
    }

    public function testMergedConfigurationContainingServiceManagerKeyWillConfigureServiceManagerPostLoadModules()
    {
        $config = array('service_manager' => $this->getServiceConfiguration());
        $configListener = new ConfigListener();
        $configListener->setMergedConfig($config);
        $this->event->setConfigListener($configListener);
        $this->assertServiceManagerIsConfigured();
    }
}
