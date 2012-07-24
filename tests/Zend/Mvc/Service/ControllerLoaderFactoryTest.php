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
use Zend\ServiceManager\ServiceManager;

class ControllerLoaderFactoryTest extends TestCase
{
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
}
