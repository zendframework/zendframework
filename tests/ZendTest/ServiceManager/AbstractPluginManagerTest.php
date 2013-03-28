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

use ReflectionClass;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

use ZendTest\ServiceManager\TestAsset\FooCounterAbstractFactory;
use ZendTest\ServiceManager\TestAsset\FooPluginManager;

class AbstractPluginManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;

    public function setup()
    {
        $this->serviceManager = new ServiceManager;
    }

    public function testSetMultipleCreationOptions()
    {
        $pluginManager = new FooPluginManager(new Config(array(
            'factories' => array(
                'Foo' => 'ZendTest\ServiceManager\TestAsset\FooFactory'
            ),
            'shared' => array(
                'Foo' => false
            )
        )));

        $refl         = new ReflectionClass($pluginManager);
        $reflProperty = $refl->getProperty('factories');
        $reflProperty->setAccessible(true);

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInternalType('string', $value['foo']);

        $pluginManager->get('Foo', array('key1' => 'value1'));

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooFactory', $value['foo']);
        $this->assertEquals(array('key1' => 'value1'), $value['foo']->getCreationOptions());

        $pluginManager->get('Foo', array('key2' => 'value2'));

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooFactory', $value['foo']);
        $this->assertEquals(array('key2' => 'value2'), $value['foo']->getCreationOptions());
    }
}
