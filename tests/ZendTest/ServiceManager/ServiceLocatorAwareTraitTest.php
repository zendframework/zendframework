<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\ServiceManager\ServiceManager;

class ServiceLocatorAwareTraitTest extends TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testSetServiceLocator()
    {
        $object = new TestAsset\MockServiceLocatorAwareTrait;

        $this->assertAttributeEquals(null, 'serviceLocator', $object);

        $serviceLocator = new ServiceManager;

        $object->setServiceLocator($serviceLocator);

        $this->assertAttributeEquals($serviceLocator, 'serviceLocator', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testGetServiceLocator()
    {
        $object = new TestAsset\MockServiceLocatorAwareTrait;

        $this->assertNull($object->getServiceLocator());

        $serviceLocator = new ServiceManager;

        $object->setServiceLocator($serviceLocator);

        $this->assertEquals($serviceLocator, $object->getServiceLocator());
    }
}
