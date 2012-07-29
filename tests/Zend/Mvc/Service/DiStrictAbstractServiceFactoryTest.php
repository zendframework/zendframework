<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\Mvc\Service;

use Zend\Mvc\Service\DiStrictAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Di\DiInstanceManagerProxy;

class DiStrictAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetAllowedServiceNames()
    {
        $instance = new DiStrictAbstractServiceFactory($this->getMock('Zend\Di\Di'));
        $instance->setAllowedServiceNames(array('first-service', 'second-service'));
        $allowedServices = $instance->getAllowedServiceNames();
        $this->assertCount(2, $allowedServices);
        $this->assertContains('first-service', $allowedServices);
        $this->assertContains('second-service', $allowedServices);
    }

    public function testWillOnlyCreateServiceInWhitelist()
    {
        $instance = new DiStrictAbstractServiceFactory($this->getMock('Zend\Di\Di'));
        $instance->setAllowedServiceNames(array('a-whitelisted-service-name'));
        $im = $instance->instanceManager();
        $im->addSharedInstance(new \stdClass(), 'a-whitelisted-service-name');
        $locator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->assertTrue($instance->canCreateServiceWithName($locator, 'a-whitelisted-service-name', 'a-whitelisted-service-name'));
        $this->assertInstanceOf('stdClass', $instance->createServiceWithName($locator, 'a-whitelisted-service-name', 'a-whitelisted-service-name'));

        $this->assertFalse($instance->canCreateServiceWithName($locator, 'not-whitelisted', 'not-whitelisted'));
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceNameException');
        $instance->createServiceWithName($locator, 'not-whitelisted', 'not-whitelisted');
    }
}
