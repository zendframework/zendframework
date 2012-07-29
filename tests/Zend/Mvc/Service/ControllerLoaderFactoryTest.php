<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Service;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\ControllerLoaderFactory;
use Zend\Mvc\Service\ControllerPluginManagerFactory;
use Zend\Mvc\Service\DiFactory;
use Zend\Mvc\Service\EventManagerFactory;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Exception;

class ControllerLoaderFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var \Zend\Mvc\Controller\ControllerManager
     */
    protected $loader;

    public function setUp()
    {
        $loaderFactory  = new ControllerLoaderFactory();
        $config         = new ArrayObject(array('di' => array()));
        $this->services = new ServiceManager();
        $this->services->setService('Zend\ServiceManager\ServiceLocatorInterface', $this->services);
        $this->services->setFactory('ControllerLoader', $loaderFactory);
        $this->services->setService('Config', $config);
        $this->services->setFactory('ControllerPluginBroker', new ControllerPluginManagerFactory());
        $this->services->setFactory('Di', new DiFactory());
        $this->services->setFactory('EventManager', new EventManagerFactory());
        $this->services->setInvokableClass('SharedEventManager', 'Zend\EventManager\SharedEventManager');

        $this->loader = $this->services->get('ControllerLoader');
    }

    public function testCannotLoadInvalidDispatchable()
    {
        // Ensure the class exists and can be autoloaded
        $this->assertTrue(class_exists('ZendTest\Mvc\Service\TestAsset\InvalidDispatchableClass'));

        try {
            $this->loader->get('ZendTest\Mvc\Service\TestAsset\InvalidDispatchableClass');
            $this->fail('Retrieving the invalid dispatchable should fail');
        } catch (\Exception $e) {
            do {
                $this->assertNotContains('Should not instantiate this', $e->getMessage());
            } while ($e = $e->getPrevious());
        }
    }

    public function testCannotLoadControllerFromPeer()
    {
        $this->services->setService('foo', $this);

        $this->setExpectedException('Zend\ServiceManager\Exception\ExceptionInterface');
        $this->loader->get('foo');
    }

    public function testControllerLoadedCanBeInjectedWithValuesFromPeer()
    {
        $config = array(
            'invokables' => array(
                'ZendTest\Dispatchable' => 'ZendTest\Mvc\Service\TestAsset\Dispatchable',
            ),
        );
        $config = new Config($config);
        $config->configureServiceManager($this->loader);

        $controller = $this->loader->get('ZendTest\Dispatchable');
        $this->assertInstanceOf('ZendTest\Mvc\Service\TestAsset\Dispatchable', $controller);
        $this->assertSame($this->services, $controller->getServiceLocator());
        $this->assertSame($this->services->get('EventManager'), $controller->getEventManager());
        $this->assertSame($this->services->get('ControllerPluginBroker'), $controller->getPluginManager());
    }

    public function testWillInstantiateControllersFromDiAbstractFactoryOnlyWhenInWhitelist()
    {
        // rewriting since controller loader does not have the correct config, but is already fetched
        $loaderFactory  = new ControllerLoaderFactory();
        $config         = new ArrayObject(array(
            'di' => array(
                'instance' => array(
                    'alias' => array(
                        'my-controller'   => 'stdClass',
                        'evil-controller' => 'stdClass',
                    ),
                ),

                'allowed_controllers' => array(
                    'my-controller',
                ),
            ),
        ));
        $this->services = new ServiceManager();
        $this->services->setService('Zend\ServiceManager\ServiceLocatorInterface', $this->services);
        $this->services->setFactory('ControllerLoader', $loaderFactory);
        $this->services->setService('Config', $config);
        $this->services->setFactory('ControllerPluginBroker', new ControllerPluginManagerFactory());
        $this->services->setFactory('Di', new DiFactory());
        $this->services->setFactory('EventManager', new EventManagerFactory());
        $this->services->setInvokableClass('SharedEventManager', 'Zend\EventManager\SharedEventManager');

        $this->loader = $this->services->get('ControllerLoader');

        $this->assertTrue($this->loader->has('my-controller'));

        try {
            $controller = $this->loader->get('my-controller');
            $this->fail('Zend\Mvc\Exception\InvalidControllerException expected');
        } catch (Exception\InvalidControllerException $e) {
            // try-catch to compact test
        }

        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->loader->get('evil-controller');
    }
}
