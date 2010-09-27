<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Loader;

use Zend\Loader\PrefixPathLoader,
    Zend\Stdlib\ArrayStack,
    SplStack;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class PrefixPathLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loader = new PrefixPathLoader();
    }

    public function badPrefixPathArguments()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array(array()),
            array(new \stdClass),
        );
    }

    public function badPrefixPathsArguments()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array(new \stdClass),
        );
    }

    public function testStackIsEmptyByDefault()
    {
        $paths = $this->loader->getPaths();
        $this->assertType('Zend\Stdlib\ArrayStack', $paths);
        $this->assertSame(0, count($paths));
    }

    /**
     * @dataProvider badPrefixPathArguments
     */
    public function testPassingNonStringPrefixToAddPrefixPathRaisesException($test)
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->addPrefixPath($test, __DIR__);
    }

    /**
     * @dataProvider badPrefixPathArguments
     */
    public function testPassingNonStringPathToAddPrefixPathRaisesException($test)
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->addPrefixPath('foo', $test);
    }

    public function testPassingValidPrefixAndPathAddsPairToRegistry()
    {
        $this->loader->addPrefixPath('foo', __DIR__);
        $paths = $this->loader->getPaths('foo');
        $this->assertType('SplStack', $paths);
        $found = false;
        foreach ($paths as $path) {
            if (rtrim(__DIR__, DIRECTORY_SEPARATOR) == rtrim($path, DIRECTORY_SEPARATOR)) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    public function testCanAddMultiplePathsToSamePrefix()
    {
        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('foo', __DIR__ . '/TestAsset');
        $paths = $this->loader->getPaths('foo');
        $this->assertType('SplStack', $paths);
        $this->assertEquals(2, count($paths));

        $expected = array(
            rtrim(realpath(__DIR__ . '/TestAsset'), DIRECTORY_SEPARATOR),
            rtrim(__DIR__, DIRECTORY_SEPARATOR), 
        );
        $test  = array();
        foreach ($paths as $path) {
            $test[] = rtrim($path, DIRECTORY_SEPARATOR);
        }
        $this->assertEquals($expected, $test);
    }

    public function testCanAddMultiplePrefixes()
    {
        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('bar', __DIR__);
        $expected = array();
        $expected['foo\\'] = array(__DIR__ . DIRECTORY_SEPARATOR);
        $expected['bar\\'] = array(__DIR__ . DIRECTORY_SEPARATOR);

        $test = $this->loader->getPaths()->getArrayCopy();
        foreach ($test as $prefix => $path) {
            $test[$prefix] = $path->toArray();
        }
        $this->assertEquals($expected, $test);
    }

    /**
     * @dataProvider badPrefixPathsArguments
     */
    public function testPassingNonArrayNonTraversableObjectToAddPrefixPathsRaisesException($arg)
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->addPrefixPaths($arg);
    }

    public function testPassingArrayPathsToAddPrefixPathsAddsManyPathsToAPrefixAtOnce()
    {
        $this->markTestIncomplete();
    }

    public function testPassingTraversablePathsToAddPrefixPathsAddsManyPathsToAPrefixAtOnce()
    {
        $this->markTestIncomplete();
    }

    public function testCanPassMultiplePrefixesToAddPrefixPaths()
    {
        $this->markTestIncomplete();
    }

    public function testCanGetPathsForASinglePrefix()
    {
        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('bar', __DIR__)
                     ->addPrefixPath('bar', __DIR__ . '/TestAsset');
        $paths = $this->loader->getPaths('foo');
        $this->assertType('SplStack', $paths);
        $this->assertEquals(1, count($paths));
    }

    public function testFalseReturnedByGetPathsWhenInvalidPrefixProvided()
    {
        $this->assertFalse($this->loader->getPaths('foo'));
    }

    public function testClearPathsReturnsTrueWhenPrefixFoundAndCleared()
    {
        $this->loader->addPrefixPath('foo', __DIR__);
        $this->assertTrue($this->loader->clearPaths('foo'));
        $paths = $this->loader->getPaths('foo');
        $this->assertFalse($paths);
    }

    public function testClearPathsReturnsFalseWhenPrefixNotFound()
    {
        $this->assertFalse($this->loader->clearPaths('foo'));
    }

    public function testClearPathsReturnsTrueWhenClearingAllPaths()
    {
        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('bar', __DIR__);
        $this->assertTrue($this->loader->clearPaths());
        $paths = $this->loader->getPaths();
        $this->assertEquals(0, count($paths));
    }

    public function testPassingInvalidPrefixToRemovePrefixPathReturnsFalse()
    {
        $this->assertFalse($this->loader->removePrefixPath('foo', __DIR__));
    }

    public function testPassingInvalidPathToRemovePrefixPathReturnsFalse()
    {
        $this->loader->addPrefixPath('foo', __DIR__);
        $this->assertFalse($this->loader->removePrefixPath('foo', __DIR__ . '/TestAsset'));
    }

    public function testPassingValidPathToRemovePrefixPathReturnsTrue()
    {
        $this->loader->addPrefixPath('foo', __DIR__);
        $this->assertTrue($this->loader->removePrefixPath('foo', __DIR__));
        $paths = $this->loader->getPaths('foo');
        $this->assertFalse($paths);

        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('foo', __DIR__ . '/TestAsset');
        $this->assertTrue($this->loader->removePrefixPath('foo', __DIR__));
        $paths = $this->loader->getPaths('foo');
        $this->assertType('SplStack', $paths);
        $this->assertEquals(1, count($paths));
        foreach ($paths as $path) {
            $this->assertContains('TestAsset', $path);
        }
    }

    public function testIsLoadedReturnsFalseIfValidPluginHasNotBeenLoaded()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->assertFalse($this->loader->isLoaded('foo'));
    }

    public function testIsLoadedReturnsFalseForInvalidPlugin()
    {
        $this->assertFalse($this->loader->isLoaded('foo'));
    }

    public function testIsLoadedReturnsTrueForValidLoadedPlugin()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->loader->load('foo');
        $this->assertTrue($this->loader->isLoaded('foo'));
    }

    public function testGetClassNameReturnsFalseForUnloadedClass()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->assertFalse($this->loader->getClassName('foo'));
    }

    public function testGetClassNameReturnsClassNameForLoadedClass()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->loader->load('foo');
        $name = $this->loader->getClassName('foo');
        $this->assertEquals('ZendTest\Loader\TestAsset\TestPlugins\Foo', $name);
    }

    public function testGetClassPathReturnsFalseForUnknownPlugin()
    {
        $this->assertFalse($this->loader->getClassName('foo'));
    }

    public function testGetClassPathReturnsFalseForUnloadedPlugin()
    {
        $this->markTestIncomplete();
    }

    public function testGetClassPathReturnsPathForLoadedPlugin()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Add this functionality to class
     */
    public function testGetClassPathsReturnsEmptyWhenNoPluginsLoaded()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Add this functionality to class
     */
    public function testGetClassPathsReturnsMapOfLoadedPluginPathPairs()
    {
        $this->markTestIncomplete();
    }

    /**
     * This is to conform to what other loaders do
     */
    public function testLoadReturnsFalseWhenUnableToResolvePlugin()
    {
        $this->assertFalse($this->loader->load('foo'));
    }

    public function testLoadReturnsClassNameWhenSuccessful()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $class = $this->loader->load('bar');
        $this->assertEquals('ZendTest\Loader\TestAsset\TestPlugins\Bar', $class);
    }

    public function testLoadPrefersAutoloadingToPath()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/plugins');
        $class = $this->loader->load('baz');
        $this->assertEquals('ZendTest\Loader\TestAsset\TestPlugins\Baz', $class);
        $r = new \ReflectionClass($class);
        $this->assertEquals(realpath(__DIR__ . '/TestAsset/TestPlugins/Baz.php'), $r->getFileName());
    }

    public function testLoadLoadsFromLastPathOnPrefixWhenAutoloadingDoesNotResolve()
    {
        $this->loader->addPrefixPath('ZendTest\PluginTest', __DIR__ . '/TestAsset/plugins/first');
        $this->loader->addPrefixPath('ZendTest\PluginTest', __DIR__ . '/TestAsset/plugins/second');
        $class = $this->loader->load('bat');
        $this->assertEquals('ZendTest\PluginTest\Bat', $class);
        $r = new \ReflectionClass($class);
        $this->assertEquals(realpath(__DIR__ . '/TestAsset/plugins/second/Bat.php'), $r->getFileName());
    }

    public function testLoadReturnsClassNameFromLatestPrefixRegistered()
    {
        $this->loader->addPrefixPath('ZendTest\PluginTest', __DIR__ . '/TestAsset/plugins/first');
        $this->loader->addPrefixPath('ZendTest\PluginTest2', __DIR__ . '/TestAsset/plugins/second');
        $class = $this->loader->load('foobar');
        $this->assertEquals('ZendTest\PluginTest2\Foobar', $class);
    }

    public function testMultipleCallsToLoadReturnPreviouslyLoadedPluginBySameName()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $expected = $this->loader->load('foo');
        $test     = $this->loader->load('foo');
        $this->assertSame($expected, $test);
    }

    public function testMultipleCallsToLoadReturnFirstPluginByNameEvenWhenLaterPathMatches()
    {
        $this->loader->addPrefixPath('ZendTest\PluginTest', __DIR__ . '/TestAsset/plugins/first');
        $expected = $this->loader->load('bat');
        $this->loader->addPrefixPath('ZendTest\PluginTest', __DIR__ . '/TestAsset/plugins/second');
        $test = $this->loader->load('bat');
        $this->assertSame($expected, $test);
        $this->assertEquals('ZendTest\PluginTest\Bat', $expected);
    }

    public function testLoadWorksWithPathsMarkedAsPrefixed()
    {
        $this->loader->addPrefixPath('ZendTest_PluginTest', __DIR__ . '/TestAsset/plugins/second', false);
        $test = $this->loader->load('foobarbaz');
        $this->assertEquals('ZendTest_PluginTest_Foobarbaz', $test);
    }
}
