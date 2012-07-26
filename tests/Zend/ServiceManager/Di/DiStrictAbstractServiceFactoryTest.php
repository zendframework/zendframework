<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Di;

use Zend\ServiceManager\Di\DiStrictAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Di\DiInstanceManagerProxy;

class DiStrictAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\ServiceManager\Di\DiStrictAbstractServiceFactory::canCreateServiceWithName
     */
    public function testCanCreateServiceWithName()
    {
        $instance = new DiStrictAbstractServiceFactory($this->getMock('Zend\Di\Di'));
        $im = $instance->instanceManager();
        $locator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        // will check shared instances
        $this->assertFalse($instance->canCreateServiceWithName($locator, 'a-shared-instance-alias', 'a-shared-instance-alias'));
        $im->addSharedInstance(new \stdClass(), 'a-shared-instance-alias');
        $this->assertTrue($instance->canCreateServiceWithName($locator, 'a-shared-instance-alias', 'a-shared-instance-alias'));

        // will check aliases
        $this->assertFalse($instance->canCreateServiceWithName($locator, 'an-alias', 'an-alias'));
        $im->addAlias('an-alias', 'stdClass');
        $this->assertTrue($instance->canCreateServiceWithName($locator, 'an-alias', 'an-alias'));

        // will check instance configurations
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Non\Existing', __NAMESPACE__ . '\Non\Existing'));
        $im->setConfig(__NAMESPACE__ . '\Non\Existing', array('parameters' => array('a' => 'b')));
        $this->assertTrue($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Non\Existing', __NAMESPACE__ . '\Non\Existing'));

        // will check preferences for abstract types
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\AbstractClass', __NAMESPACE__ . '\AbstractClass'));
        $im->setTypePreference(__NAMESPACE__ . '\AbstractClass', array(__NAMESPACE__ . '\Non\Existing'));
        $this->assertTrue($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\AbstractClass', __NAMESPACE__ . '\AbstractClass'));

        // will check definitions
        $def = $instance->definitions();
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Other\Non\Existing', __NAMESPACE__ . '\Other\Non\Existing'));
        $classDefinition = $this->getMock('Zend\Di\Definition\DefinitionInterface');
        $classDefinition
            ->expects($this->any())
            ->method('hasClass')
            ->with($this->equalTo(__NAMESPACE__ . '\Other\Non\Existing'))
            ->will($this->returnValue(true));
        $def->addDefinition($classDefinition);
        $this->assertFalse($instance->canCreateServiceWithName($locator, __NAMESPACE__ . '\Other\Non\Existing', __NAMESPACE__ . '\Other\Non\Existing'));
    }
}
