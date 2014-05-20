<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ServiceManagerConfigTest extends TestCase
{
    public function setUp()
    {
        $this->config = new ServiceManagerConfig();
        $this->services = new ServiceManager();
        $this->config->configureServiceManager($this->services);
    }

    /**
     * @group 3786
     */
    public function testEventManagerAwareInterfaceIsNotInjectedIfPresentButSharedManagerIs()
    {
        $events = new EventManager();
        TestAsset\EventManagerAwareObject::$defaultEvents = $events;

        $this->services->setInvokableClass('EventManagerAwareObject', __NAMESPACE__ . '\TestAsset\EventManagerAwareObject');

        $instance = $this->services->get('EventManagerAwareObject');
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\EventManagerAwareObject', $instance);
        $this->assertSame($events, $instance->getEventManager());
        $this->assertSame($this->services->get('SharedEventManager'), $events->getSharedManager());
    }

    public function testCanMergeCustomConfigWithDefaultConfig()
    {
        $custom = array(
            'invokables' => array(
                'foo' => '\stdClass',
            ),
            'factories' => array(
                'bar' => function () {
                    return new \stdClass();
                },
            ),
        );

        $config = new ServiceManagerConfig($custom);
        $sm = new ServiceManager();
        $config->configureServiceManager($sm);

        $this->assertTrue($sm->has('foo'));
        $this->assertTrue($sm->has('bar'));
        $this->assertTrue($sm->has('ModuleManager'));
    }

    public function testCanOverrideDefaultConfigWithCustomConfig()
    {
        $custom = array(
            'invokables' => array(
                'foo' => '\stdClass',
            ),
            'factories' => array(
                'ModuleManager' => function () {
                    return new \stdClass();
                },
            ),
        );

        $config = new ServiceManagerConfig($custom);
        $sm = new ServiceManager();
        $config->configureServiceManager($sm);

        $this->assertTrue($sm->has('foo'));
        $this->assertTrue($sm->has('ModuleManager'));

        $this->assertInstanceOf('stdClass', $sm->get('ModuleManager'));
    }

    public function testCanAddDelegators()
    {
        $config = array(
            'invokables' => array(
                'foo' => '\stdClass',
            ),
            'delegators' => array(
                'foo' => array(
                    function (ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback) {
                        $service = $callback();
                        $service->bar = 'baz';

                        return $service;
                    },
                )
            ),
        );

        $config = new ServiceManagerConfig($config);
        $sm = new ServiceManager();
        $config->configureServiceManager($sm);

        $std = $sm->get('foo');
        $this->assertInstanceOf('stdClass', $std);
        $this->assertEquals('baz', $std->bar);
    }
}
