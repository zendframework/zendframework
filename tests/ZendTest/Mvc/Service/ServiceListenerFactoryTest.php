<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\ServiceListenerFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc_Service
 * @subpackage UnitTest
 */
class ServiceListenerFactoryTest extends TestCase
{
    public function setUp()
    {
        $sm = $this->sm = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
                               ->setMethods(array('get'))
                               ->getMock();

        $this->factory  = new ServiceListenerFactory();
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value of service_listener_options must be an array, string given.
     */
    public function testInvalidOptionType()
    {
        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue(array('service_listener_options' => 'string')));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, 0 array must contain service_manager key.
     */
    public function testMissingServiceManager()
    {
        $config['service_listener_options'][0]['service_manager'] = null;
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, service_manager must be a string, integer given.
     */
    public function testInvalidTypeServiceManager()
    {
        $config['service_listener_options'][0]['service_manager'] = 1;
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, 0 array must contain config_key key.
     */
    public function testMissingConfigKey()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = null;
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, config_key must be a string, integer given.
     */
    public function testInvalidTypeConfigKey()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = 1;
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, 0 array must contain interface key.
     */
    public function testMissingInterface()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = null;
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, interface must be a string, integer given.
     */
    public function testInvalidTypeInterface()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = 1;
        $config['service_listener_options'][0]['method']          = 'test';

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, 0 array must contain method key.
     */
    public function testMissingMethod()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = null;

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }

    /**
     * @expectedException        Zend\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid service listener options detected, method must be a string, integer given.
     */
    public function testInvalidTypeMethod()
    {
        $config['service_listener_options'][0]['service_manager'] = 'test';
        $config['service_listener_options'][0]['config_key']      = 'test';
        $config['service_listener_options'][0]['interface']       = 'test';
        $config['service_listener_options'][0]['method']          = 1;

        $this->sm->expects($this->once())
                 ->method('get')
                 ->will($this->returnValue($config));

        $this->factory->createService($this->sm);
    }
}
