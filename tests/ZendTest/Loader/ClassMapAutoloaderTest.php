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

use Zend\Loader\ClassMapAutoloader;
use Zend\Loader\Exception\InvalidArgumentException;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Loader
 */
class ClassMapAutoloaderTest extends \PHPUnit_Framework_TestCase
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

        $this->loader = new ClassMapAutoloader();
    }

    public function tearDown()
    {
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

    public function testRegisteringNonExistentAutoloadMapRaisesInvalidArgumentException()
    {
        $dir = __DIR__ . '__foobar__';
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->registerAutoloadMap($dir);
    }

    public function testValidMapFileNotReturningMapRaisesInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/badmap.php');
    }

    public function testAllowsRegisteringArrayAutoloadMapDirectly()
    {
        $map = array(
            'Zend\Loader\Exception\ExceptionInterface' => __DIR__ . '/../../../library/Zend/Loader/Exception/ExceptionInterface.php',
        );
        $this->loader->registerAutoloadMap($map);
        $test = $this->loader->getAutoloadMap();
        $this->assertSame($map, $test);
    }

    public function testAllowsRegisteringArrayAutoloadMapViaConstructor()
    {
        $map = array(
            'Zend\Loader\Exception\ExceptionInterface' => __DIR__ . '/../../../library/Zend/Loader/Exception/ExceptionInterface.php',
        );
        $loader = new ClassMapAutoloader(array($map));
        $test = $loader->getAutoloadMap();
        $this->assertSame($map, $test);
    }

    public function testRegisteringValidMapFilePopulatesAutoloader()
    {
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');
        $map = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, count($map));
        // Just to make sure nothing changes after loading the same map again
        // (loadMapFromFile should just return)
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');
        $map = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, count($map));
    }

    public function testRegisteringMultipleMapsMergesThem()
    {
        $map = array(
            'Zend\Loader\Exception\ExceptionInterface' => __DIR__ . '/../../../library/Zend/Loader/Exception/ExceptionInterface.php',
            'ZendTest\Loader\StandardAutoloaderTest' => 'some/bogus/path.php',
        );
        $this->loader->registerAutoloadMap($map);
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');

        $test = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test));
        $this->assertNotEquals($map['ZendTest\Loader\StandardAutoloaderTest'], $test['ZendTest\Loader\StandardAutoloaderTest']);
    }

    public function testCanRegisterMultipleMapsAtOnce()
    {
        $map = array(
            'Zend\Loader\Exception\ExceptionInterface' => __DIR__ . '/../../../library/Zend/Loader/Exception/ExceptionInterface.php',
            'ZendTest\Loader\StandardAutoloaderTest' => 'some/bogus/path.php',
        );
        $maps = array($map, __DIR__ . '/_files/goodmap.php');
        $this->loader->registerAutoloadMaps($maps);
        $test = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test));
    }

    public function testRegisterMapsThrowsExceptionForNonTraversableArguments()
    {
        $tests = array(true, 'string', 1, 1.0, new \stdClass);
        foreach ($tests as $test) {
            try {
                $this->loader->registerAutoloadMaps($test);
                $this->fail('Should not register non-traversable arguments');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('array or implement Traversable', $e->getMessage());
            }
        }
    }

    public function testAutoloadLoadsClasses()
    {
        $map = array('ZendTest\UnusualNamespace\ClassMappedClass' => __DIR__ . '/TestAsset/ClassMappedClass.php');
        $this->loader->registerAutoloadMap($map);
        $loaded = $this->loader->autoload('ZendTest\UnusualNamespace\ClassMappedClass');
        $this->assertSame('ZendTest\UnusualNamespace\ClassMappedClass', $loaded);
        $this->assertTrue(class_exists('ZendTest\UnusualNamespace\ClassMappedClass', false));
    }

    public function testIgnoresClassesNotInItsMap()
    {
        $map = array('ZendTest\UnusualNamespace\ClassMappedClass' => __DIR__ . '/TestAsset/ClassMappedClass.php');
        $this->loader->registerAutoloadMap($map);
        $this->assertFalse($this->loader->autoload('ZendTest\UnusualNamespace\UnMappedClass'));
        $this->assertFalse(class_exists('ZendTest\UnusualNamespace\UnMappedClass', false));
    }

    public function testRegisterRegistersCallbackWithSplAutoload()
    {
        $this->loader->register();
        $loaders = spl_autoload_functions();
        $this->assertTrue(count($this->loaders) < count($loaders));
        $test = array_shift($loaders);
        $this->assertEquals(array($this->loader, 'autoload'), $test);
    }

    public function testCanLoadClassMapFromPhar()
    {
        $map = 'phar://' . __DIR__ . '/_files/classmap.phar/test/.//../autoload_classmap.php';
        $this->loader->registerAutoloadMap($map);
        $loaded = $this->loader->autoload('some\loadedclass');
        $this->assertSame('some\loadedclass', $loaded);
        $this->assertTrue(class_exists('some\loadedclass', false));

        // will not register duplicate, even with a different relative path
        $map = 'phar://' . __DIR__ . '/_files/classmap.phar/test/./foo/../../autoload_classmap.php';
        $this->loader->registerAutoloadMap($map);
        $test = $this->loader->getAutoloadMap();
        $this->assertEquals(1, count($test));
    }

}
