<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\ServiceManager\ServiceManager;

/**
 * @requires PHP 5.4
 */
class ServiceLocatorAwareTraitTest extends TestCase
{
    public function testSetServiceLocator()
    {
        $object = $this->getObjectForTrait('\Zend\ServiceManager\ServiceLocatorAwareTrait');

        $this->assertAttributeEquals(null, 'serviceLocator', $object);

        $serviceLocator = new ServiceManager;

        $object->setServiceLocator($serviceLocator);

        $this->assertAttributeEquals($serviceLocator, 'serviceLocator', $object);
    }

    public function testGetServiceLocator()
    {
        $object = $this->getObjectForTrait('\Zend\ServiceManager\ServiceLocatorAwareTrait');

        $this->assertNull($object->getServiceLocator());

        $serviceLocator = new ServiceManager;

        $object->setServiceLocator($serviceLocator);

        $this->assertEquals($serviceLocator, $object->getServiceLocator());
    }
}
