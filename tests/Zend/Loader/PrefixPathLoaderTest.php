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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\PrefixPathLoader,
    Zend\Stdlib\ArrayStack,
    SplStack;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class PrefixPathLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Ensure any statically created paths are cleared
        PrefixPathLoader::addStaticPaths(null);
        TestAsset\ExtendedPrefixPathLoader::addStaticPaths(null);

        // Create instance of the loader
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
        $this->assertInstanceOf('Zend\Stdlib\ArrayStack', $paths);
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
        $this->assertInstanceOf('SplStack', $paths);
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
                     ->addPrefixPath('foo', __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset');
        $paths = $this->loader->getPaths('foo');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertEquals(2, count($paths));

        $expected = array(
            rtrim(realpath(__DIR__ . DIRECTORY_SEPARATOR . 'TestAsset'), DIRECTORY_SEPARATOR),
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

    public function testPassingArrayPathsToAddPrefixPathsAddsManyPathsAtOnce()
    {
        $path1 = array('prefix' => 'foo', 'path'   => __DIR__);
        $path2 = array('prefix' => 'foo', 'path'   => __DIR__ . '/TestAsset');
        $path3 = array('prefix' => 'bar', 'path'   => __DIR__, 'namespaced' => false);
        $path3 = (object) $path3;
        $paths = array($path1, $path2, $path3);
        $this->loader->addPrefixPaths($paths);
        $test = $this->loader->getPaths();
        $this->assertTrue(isset($test['foo\\']));
        $this->assertTrue(isset($test['bar_']));

        $path = $test['foo\\'];
        $this->assertEquals(2, count($path));
        $testPaths = array();
        foreach ($path as $p) {
            $testPaths[] = $p;
        }
        foreach (array($path1['path'], $path2['path']) as $p) {
            $this->assertContains($p . DIRECTORY_SEPARATOR, $testPaths, var_export($testPaths, 1));
        }

        $path = $test['bar_'];
        $this->assertEquals(1, count($path));
        foreach ($path as $p) {
            $this->assertContains($path3->path, $p);
        }
    }

    public function testPassingTraversablePathsToAddPrefixPathsAddsManyPathsAtOnce()
    {
        $path1 = array('prefix' => 'foo', 'path'   => __DIR__);
        $path2 = array('prefix' => 'foo', 'path'   => __DIR__ . '/TestAsset');
        $path3 = array('prefix' => 'bar', 'path'   => __DIR__, 'namespaced' => false);
        $path3 = (object) $path3;
        $paths = array($path1, $path2, $path3);
        $paths = new \ArrayObject($paths);

        $this->loader->addPrefixPaths($paths);
        $test = $this->loader->getPaths();
        $this->assertTrue(isset($test['foo\\']));
        $this->assertTrue(isset($test['bar_']));

        $path = $test['foo\\'];
        $this->assertEquals(2, count($path));
        $testPaths = array();
        foreach ($path as $p) {
            $testPaths[] = $p;
        }
        foreach (array($path1['path'], $path2['path']) as $p) {
            $this->assertContains($p . DIRECTORY_SEPARATOR, $testPaths, var_export($testPaths, 1));
        }

        $path = $test['bar_'];
        $this->assertEquals(1, count($path));
        foreach ($path as $p) {
            $this->assertContains($path3->path, $p);
        }
    }

    public function testCanGetPathsForASinglePrefix()
    {
        $this->loader->addPrefixPath('foo', __DIR__)
                     ->addPrefixPath('bar', __DIR__)
                     ->addPrefixPath('bar', __DIR__ . '/TestAsset');
        $paths = $this->loader->getPaths('foo');
        $this->assertInstanceOf('SplStack', $paths);
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
        $this->assertInstanceOf('SplStack', $paths);
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

    public function testGetPluginMapReturnsEmptyArrayByDefault()
    {
        $map = $this->loader->getPluginMap();
        $this->assertTrue(empty($map));
    }

    public function testGetPluginMapReturnsListOfPluginsMappingToClasses()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins2', __DIR__ . '/TestAsset/TestPlugins2');
        $this->loader->load('foo');
        $this->loader->load('bar');
        $expected = array(
            'Foo' => 'ZendTest\Loader\TestAsset\TestPlugins2\Foo',
            'Bar' => 'ZendTest\Loader\TestAsset\TestPlugins\Bar',
        );
        $this->assertEquals($expected, $this->loader->getPluginMap());
    }

    public function testGetClassMapReturnsEmptyArrayByDefault()
    {
        $map = $this->loader->getClassMap();
        $this->assertTrue(empty($map));
    }

    public function testGetClassMapReturnsListOfClassNamesMappingToFilenames()
    {
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins', __DIR__ . '/TestAsset/TestPlugins');
        $this->loader->addPrefixPath('ZendTest\Loader\TestAsset\TestPlugins2', __DIR__ . '/TestAsset/TestPlugins2');
        $this->loader->load('foo');
        $this->loader->load('bar');
        $expected = array(
            'ZendTest\Loader\TestAsset\TestPlugins2\Foo' => realpath(__DIR__ . '/TestAsset/TestPlugins2/Foo.php'),
            'ZendTest\Loader\TestAsset\TestPlugins\Bar'  => realpath(__DIR__ . '/TestAsset/TestPlugins/Bar.php'),
        );
        $this->assertEquals($expected, $this->loader->getClassMap());
    }

    public function testAddingStaticPathsDoesNotAffectExistingIntances()
    {
        PrefixPathLoader::addStaticPaths(array(
            array('prefix' => 'ZendTest\Loader\TestAsset', 'path' => __DIR__ . '/TestAsset'),
        ));
        $this->assertFalse($this->loader->getPaths('ZendTest\Loader\TestAsset'));
    }

    public function testAllowsAddingStaticPathsForSeedingInstances()
    {
        PrefixPathLoader::addStaticPaths(array(
            array('prefix' => 'ZendTest\Loader\TestAsset', 'path' => __DIR__ . '/TestAsset'),
        ));
        $loader = new PrefixPathLoader();
        $paths = $loader->getPaths('ZendTest\Loader\TestAsset');
        $this->assertEquals(1, count($paths));
    }

    public function testPassingNullToStaticPathsClearsStaticPaths()
    {
        PrefixPathLoader::addStaticPaths(null);
        $loader = new PrefixPathLoader();
        $this->assertFalse($loader->getPaths('ZendTest\Loader\TestAsset'));
    }

    public function testAddingStaticPathsAllowsSameArgumentsAsAddPrefixPaths()
    {
        // array of paths
        $path1 = array('prefix' => 'foo', 'path'   => __DIR__);
        $path2 = array('prefix' => 'foo', 'path'   => __DIR__ . '/TestAsset');
        $path3 = array('prefix' => 'bar', 'path'   => __DIR__, 'namespaced' => false);
        $path3 = (object) $path3;
        $paths = array($path1, $path2, $path3);
        PrefixPathLoader::addStaticPaths($paths);
        $loader = new PrefixPathLoader();
        $test = $loader->getPaths();
        $this->assertTrue(isset($test['foo\\']));
        $this->assertTrue(isset($test['bar_']));

        PrefixPathLoader::addStaticPaths(null);

        // Traversable object of paths
        $path1 = array('prefix' => 'foo', 'path'   => __DIR__);
        $path2 = array('prefix' => 'foo', 'path'   => __DIR__ . '/TestAsset');
        $path3 = array('prefix' => 'bar', 'path'   => __DIR__, 'namespaced' => false);
        $path3 = (object) $path3;
        $paths = array($path1, $path2, $path3);
        $paths = new \ArrayObject($paths);
        PrefixPathLoader::addStaticPaths($paths);
        $loader = new PrefixPathLoader();
        $test = $loader->getPaths();
        $this->assertTrue(isset($test['foo\\']));
        $this->assertTrue(isset($test['bar_']));
    }

    public function testMulitipleCallsToAddStaticPathsMergesPaths()
    {
        PrefixPathLoader::addStaticPaths(array(
            array('prefix' => 'foo', 'path' => __DIR__),
        ));
        PrefixPathLoader::addStaticPaths(array(
            array('prefix' => 'foo', 'path' => __DIR__ . '/TestAsset'),
            array('prefix' => 'bar', 'path' => __DIR__ . '/TestAsset/plugins'),
        ));
        $loader = new PrefixPathLoader();
        $paths = $loader->getPaths();

        $this->assertTrue(isset($paths['foo\\']));
        $foo = $paths['foo\\'];
        $this->assertEquals(2, count($foo));

        $this->assertTrue(isset($paths['bar\\']));
        $foo = $paths['bar\\'];
        $this->assertEquals(1, count($foo));
    }

    public function testStaticPathsUsesLateStaticBinding()
    {
        TestAsset\ExtendedPrefixPathLoader::addStaticPaths(array(
            array('prefix' => 'foo', 'path' => __DIR__),
        ));
        $loader = new PrefixPathLoader();
        $this->assertFalse($loader->getPaths('foo'));

        $loader = new TestAsset\ExtendedPrefixPathLoader();
        $paths  = $loader->getPaths('foo');
        $this->assertEquals(1, count($paths));
    }

    public function testPathPrecedenceIsExplicitTrumpsConstructorTrumpsStaticTrumpsInternal()
    {
        $loader = new TestAsset\ExtendedPrefixPathLoader();
        $paths  = $loader->getPaths('loader');
        $this->assertEquals(1, count($paths));
        foreach ($paths as $path) {
            $this->assertContains(__DIR__ . DIRECTORY_SEPARATOR . 'TestAsset', $path);
        }

        TestAsset\ExtendedPrefixPathLoader::addStaticPaths(
            array(
                 array('prefix' => 'loader',
                       'path'   => __DIR__),
            ));
        $loader = new TestAsset\ExtendedPrefixPathLoader();
        $paths  = $loader->getPaths('loader');
        $this->assertEquals(2, count($paths));
        $test     = $paths->toArray();
        $expected = array(__DIR__ . DIRECTORY_SEPARATOR,
                          __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR);
        $this->assertSame($expected, $test);

        $loader = new TestAsset\ExtendedPrefixPathLoader(
            array(
                 array('prefix' => 'loader',
                       'path'   =>
                       __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' .
                       DIRECTORY_SEPARATOR . 'plugins'),
            ));
        $paths  = $loader->getPaths('loader');
        $this->assertEquals(3, count($paths));
        $test     = $paths->toArray();
        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
            __DIR__ . DIRECTORY_SEPARATOR, __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR);
        $this->assertSame($expected, $test);

        $loader = new TestAsset\ExtendedPrefixPathLoader(
            array(
                 array('prefix' => 'loader',
                       'path'   =>
                       __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' .
                       DIRECTORY_SEPARATOR . 'plugins'),
            ));
        $loader->addPrefixPath('loader', __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' .
                                         DIRECTORY_SEPARATOR . 'TestNamespace');
        $paths = $loader->getPaths('loader');
        $this->assertEquals(4, count($paths));
        $test     = $paths->toArray();
        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR . 'TestNamespace' . DIRECTORY_SEPARATOR,
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
            __DIR__ . DIRECTORY_SEPARATOR, __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR);
        $this->assertSame($expected, $test);
    }
}
