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
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Loader_PluginLoaderTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Loader/PluginLoader.php';

/**
 * Test class for Zend_Loader_PluginLoader.
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class Zend_Loader_PluginLoaderTest extends PHPUnit_Framework_TestCase
{
    protected $_includeCache;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Loader_PluginLoaderTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (file_exists($this->_includeCache)) {
            unlink($this->_includeCache);
        }
        Zend_Loader_PluginLoader::setIncludeFileCache(null);
        $this->_includeCache = dirname(__FILE__) . '/_files/includeCache.inc.php';
        $this->libPath = realpath(dirname(__FILE__) . '/../../../library');
        $this->key = null;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->clearStaticPaths();
        Zend_Loader_PluginLoader::setIncludeFileCache(null);
        if (file_exists($this->_includeCache)) {
            unlink($this->_includeCache);
        }
    }

    public function clearStaticPaths()
    {
        if (null !== $this->key) {
            $loader = new Zend_Loader_PluginLoader(array(), $this->key);
            $loader->clearPaths();
        }
    }

    public function testAddPrefixPathNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, count($paths['Zend_View_']));
        $this->assertEquals(2, count($paths['Zend_Loader_']));
    }

    public function testAddPrefixPathMultipleTimes()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader');
        $paths = $loader->getPaths();

        $this->assertType('array', $paths);
        $this->assertEquals(1, count($paths['Zend_Loader_']));
    }

    public function testAddPrefixPathStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, count($paths['Zend_View_']));
        $this->assertEquals(2, count($paths['Zend_Loader_']));
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPrefix()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->addPrefixPath(array(), $this->libPath);
            $this->fail('addPrefixPath() should throw exception with non-string prefix');
        } catch (Exception $e) {
        }
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPath()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->addPrefixPath('Foo_Bar', array());
            $this->fail('addPrefixPath() should throw exception with non-string path');
        } catch (Exception $e) {
        }
    }

    public function testRemoveAllPathsForGivenPrefixNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths('Zend_Loader');
        $this->assertEquals(2, count($paths));
        $loader->removePrefixPath('Zend_Loader');
        $this->assertFalse($loader->getPaths('Zend_Loader'));
    }

    public function testRemoveAllPathsForGivenPrefixStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths('Zend_Loader');
        $this->assertEquals(2, count($paths));
        $loader->removePrefixPath('Zend_Loader');
        $this->assertFalse($loader->getPaths('Zend_Loader'));
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->removePrefixPath('Foo_Bar');
            $this->fail('Removing non-existent prefix should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixPathPairNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Bar', realpath(dirname(__FILE__)));
        $paths = $loader->getPaths();
        $this->assertTrue(isset($paths['Foo_Bar_']));
        try {
            $loader->removePrefixPath('Foo_Bar', $this->libPath);
            $this->fail('Removing non-existent prefix/path pair should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testClearPathsNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, count($paths));
    }

    public function testClearPathsStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, count($paths));
    }

    public function testClearPathsWithPrefixNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths('Zend_Loader');
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
    }

    public function testClearPathsWithPrefixStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths('Zend_Loader');
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
    }

    public function testGetClassNameNonStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testGetClassNameStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testLoadPluginNonStaticallyLoadsClass()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormButton');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend_View_Helper_FormButton', $className);
        $this->assertTrue(class_exists('Zend_View_Helper_FormButton', false));
        $this->assertTrue($loader->isLoaded('FormButton'));
    }

    public function testLoadPluginStaticallyLoadsClass()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormRadio');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend_View_Helper_FormRadio', $className);
        $this->assertTrue(class_exists('Zend_View_Helper_FormRadio', false));
        $this->assertTrue($loader->isLoaded('FormRadio'));
    }

    public function testLoadThrowsExceptionIfFileFoundInPrefixButClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('Doctype');
            $this->fail('Invalid prefix for a path should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testLoadThrowsExceptionIfNoHelperClassLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FooBarBazBat');
            $this->fail('Not finding a helper should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testGetClassAfterNonStaticLoadReturnsResolvedClassName()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormSelect');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSelect'));
        $this->assertEquals('Zend_View_Helper_FormSelect', $loader->getClassName('FormSelect'));
    }

    public function testGetClassAfterStaticLoadReturnsResolvedClassName()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormCheckbox');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormCheckbox'));
        $this->assertEquals('Zend_View_Helper_FormCheckbox', $loader->getClassName('FormCheckbox'));
    }

    public function testClassFilesAreSearchedInLifoOrder()
    {
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', dirname(__FILE__) . '/_files/ZfTest');
        try {
            $className = $loader->load('FormSubmit');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSubmit'));
        $this->assertEquals('ZfTest_FormSubmit', $loader->getClassName('FormSubmit'));
    }

    /**
     * @issue ZF-2741
     */
    public function testWin32UnderscoreSpacedShortNamesWillLoad()
    {
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_Filter', $this->libPath . '/Zend/Filter');
        try {
            // Plugin loader will attempt to load "c:\path\to\library/Zend/Filter/Word\UnderscoreToDash.php"
            $className = $loader->load('Word_UnderscoreToDash');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('Word_UnderscoreToDash'));
    }

    /**
     * @group ZF-4670
     */
    public function testIncludeCacheShouldBeNullByDefault()
    {
        $this->assertNull(Zend_Loader_PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldAllowSpecifyingIncludeFileCache()
    {
        $cacheFile = $this->_includeCache;
        $this->testIncludeCacheShouldBeNullByDefault();
        Zend_Loader_PluginLoader::setIncludeFileCache($cacheFile);
        $this->assertEquals($cacheFile, Zend_Loader_PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     * @expectedException Zend_Loader_PluginLoader_Exception
     */
    public function testPluginLoaderShouldThrowExceptionWhenPathDoesNotExist()
    {
        $cacheFile = dirname(__FILE__) . '/_filesDoNotExist/includeCache.inc.php';
        $this->testIncludeCacheShouldBeNullByDefault();
        Zend_Loader_PluginLoader::setIncludeFileCache($cacheFile);
        $this->fail('Should not allow specifying invalid cache file path');
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldAppendIncludeCacheWhenClassIsFound()
    {
        $cacheFile = $this->_includeCache;
        Zend_Loader_PluginLoader::setIncludeFileCache($cacheFile);
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', dirname(__FILE__) . '/_files/ZfTest');
        try {
            $className = $loader->load('CacheTest');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertTrue(file_exists($cacheFile));
        $cache = file_get_contents($cacheFile);
        $this->assertContains('CacheTest.php', $cache);
    }

    /**
     * @group ZF-5208
     */
    public function testStaticRegistryNamePersistsInDifferentLoaderObjects()
    {
        $loader1 = new Zend_Loader_PluginLoader(array(), "PluginLoaderStaticNamespace");
        $loader1->addPrefixPath("Zend_View_Helper", "Zend/View/Helper");

        $loader2 = new Zend_Loader_PluginLoader(array(), "PluginLoaderStaticNamespace");
        $this->assertEquals(array(
            "Zend_View_Helper_" => array("Zend/View/Helper/"),
        ), $loader2->getPaths());
    }

    /**
     * @group ZF-4697
     */
    public function testClassFilesGrabCorrectPathForLoadedClasses()
    {
        require_once 'Zend/View/Helper/DeclareVars.php';
        $reflection = new ReflectionClass('Zend_View_Helper_DeclareVars');
        $expected   = $reflection->getFileName();
        
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', dirname(__FILE__) . '/_files/ZfTest');
        try {
            // Class in /Zend/View/Helper and not in /_files/ZfTest
            $className = $loader->load('DeclareVars');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        
        $classPath = $loader->getClassPath('DeclareVars');
        $this->assertContains($expected, $classPath);
    }
}

// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD === 'Zend_Loader_PluginLoaderTest::main') {
    Zend_Loader_PluginLoaderTest::main();
}
