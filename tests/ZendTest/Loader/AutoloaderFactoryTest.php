<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

namespace ZendTest\Loader;

use ReflectionClass;
use Zend\Loader\AutoloaderFactory;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Loader
 */
class AutoloaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();
    }

    public function tearDown()
    {
        AutoloaderFactory::unregisterAutoloaders();
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testRegisteringValidMapFilePopulatesAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/_files/goodmap.php',
            ),
        ));
        $loader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\ClassMapAutoloader');
        $map = $loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, count($map));
    }

    /**
     * This tests checks if invalid autoloaders cause exceptions
     *
     * @expectedException InvalidArgumentException
     */
    public function testFactoryCatchesInvalidClasses()
    {
        if (!version_compare(PHP_VERSION, '5.3.7', '>=')) {
            $this->markTestSkipped('Cannot test invalid interface loader with versions less than 5.3.7');
        }
        include __DIR__ . '/_files/InvalidInterfaceAutoloader.php';
        AutoloaderFactory::factory(array(
            'InvalidInterfaceAutoloader' => array()
        ));
    }

    public function testFactoryDoesNotRegisterDuplicateAutoloaders()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'ZendTest\Loader\TestAsset\TestPlugins' => __DIR__ . '/TestAsset/TestPlugins',
                ),
            ),
        ));
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
        $this->assertTrue(class_exists('TestNamespace\NoDuplicateAutoloadersCase'));
        $this->assertTrue(class_exists('ZendTest\Loader\TestAsset\TestPlugins\Foo'));
    }

    public function testCanUnregisterAutoloaders()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        AutoloaderFactory::unregisterAutoloaders();
        $this->assertEquals(0, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testCanUnregisterAutoloadersByClassName()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        AutoloaderFactory::unregisterAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertEquals(0, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testCanGetValidRegisteredAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        $autoloader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertInstanceOf('Zend\Loader\StandardAutoloader', $autoloader);
    }

    public function testDefaultAutoloader()
    {
        AutoloaderFactory::factory();
        $autoloader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertInstanceOf('Zend\Loader\StandardAutoloader', $autoloader);
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testGetInvalidAutoloaderThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $loader = AutoloaderFactory::getRegisteredAutoloader('InvalidAutoloader');
    }

    public function testFactoryWithInvalidArgumentThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        AutoloaderFactory::factory('InvalidArgument');
    }

    public function testFactoryWithInvalidAutoloaderClassThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        AutoloaderFactory::factory(array('InvalidAutoloader' => array()));
    }

    public function testCannotBeInstantiatedViaConstructor()
    {
        $reflection = new ReflectionClass('Zend\Loader\AutoloaderFactory');
        $constructor = $reflection->getConstructor();
        $this->assertNull($constructor);
    }

    public function testPassingNoArgumentsToFactoryInstantiatesAndRegistersStandardAutoloader()
    {
        AutoloaderFactory::factory();
        $loaders = AutoloaderFactory::getRegisteredAutoloaders();
        $this->assertEquals(1, count($loaders));
        $loader = array_shift($loaders);
        $this->assertInstanceOf('Zend\Loader\StandardAutoloader', $loader);

        $test  = array($loader, 'autoload');
        $found = false;
        foreach (spl_autoload_functions() as $function) {
            if ($function === $test) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'StandardAutoloader not registered with spl_autoload');
    }
}
