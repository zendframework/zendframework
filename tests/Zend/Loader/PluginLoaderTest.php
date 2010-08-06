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

namespace ZendTest\Loader;

use \Zend\Loader\Autoloader,
    \Zend\Loader\PluginLoader;

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
class PluginLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $_includeCache;

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

        // Possible for previous tests to remove autoloader 
        Autoloader::resetInstance();
        $al = Autoloader::getInstance();
        $al->registerPrefix('PHPUnit_');

        PluginLoader::setIncludeFileCache(null);
        $this->_includeCache = __DIR__ . '/_files/includeCache.inc.php';
        $this->libPath = realpath(__DIR__ . '/../../../library');
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
        PluginLoader::setIncludeFileCache(null);
        if (file_exists($this->_includeCache)) {
            unlink($this->_includeCache);
        }
    }

    public function clearStaticPaths()
    {
        if (null !== $this->key) {
            $loader = new PluginLoader(array(), $this->key);
            $loader->clearPaths();
        }
    }

    public function testAddPrefixPathNonStatically()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend\View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend\Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend\Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend\View\\', $paths));
        $this->assertTrue(array_key_exists('Zend\Loader\\', $paths));
        $this->assertEquals(1, count($paths['Zend\View\\']));
        $this->assertEquals(2, count($paths['Zend\Loader\\']));
    }

    public function testAddPrefixPathMultipleTimes()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend\Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend\Loader', $this->libPath . '/Zend/Loader');
        $paths = $loader->getPaths();

        $this->assertType('array', $paths);
        $this->assertEquals(1, count($paths['Zend\Loader\\']));
    }

    public function testAddPrefixPathStatically()
    {
        $this->key = 'foobar';
        $loader = new PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend\View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend\Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend\Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend\View\\', $paths));
        $this->assertTrue(array_key_exists('Zend\Loader\\', $paths));
        $this->assertEquals(1, count($paths['Zend\View\\']));
        $this->assertEquals(2, count($paths['Zend\Loader\\']));
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPrefix()
    {
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'only takes strings');
        $loader = new PluginLoader();
        $loader->addPrefixPath(array(), $this->libPath);
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPath()
    {
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'only takes strings');
        $loader = new PluginLoader();
        $loader->addPrefixPath('Foo_Bar', array());
    }

    public function testRemoveAllPathsForGivenPrefixNonStatically()
    {
        $loader = new PluginLoader();
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
        $loader = new PluginLoader(array(), $this->key);
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
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'not found');
        $loader = new PluginLoader();
        $loader->removePrefixPath('Foo_Bar');
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixPathPairNotRegistered()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Foo\Bar', realpath(__DIR__));
        $paths = $loader->getPaths();
        $this->assertTrue(isset($paths['Foo\Bar\\']));
        $this->setExpectedException('Zend\Loader\PluginLoaderException');
        $loader->removePrefixPath('Foo\Bar', $this->libPath);
    }

    public function testClearPathsNonStaticallyClearsPathArray()
    {
        $loader = new PluginLoader();
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
        $loader = new PluginLoader(array(), $this->key);
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
        $loader = new PluginLoader();
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
        $loader = new PluginLoader(array(), $this->key);
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
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testGetClassNameStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $this->key = 'foobar';
        $loader = new PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testLoadPluginNonStaticallyLoadsClass()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormButton');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend\View\Helper\FormButton', $className);
        $this->assertTrue(class_exists('Zend\View\Helper\FormButton', false));
        $this->assertTrue($loader->isLoaded('FormButton'));
    }

    public function testLoadPluginStaticallyLoadsClass()
    {
        $this->key = 'foobar';
        $loader = new PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormRadio');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend\View\Helper\FormRadio', $className);
        $this->assertTrue(class_exists('Zend\View\Helper\FormRadio', false));
        $this->assertTrue($loader->isLoaded('FormRadio'));
    }

    public function testLoadThrowsExceptionIfFileFoundInPrefixButClassNotLoaded()
    {
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'not found in the registry');
        $loader = new PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        $className = $loader->load('Doctype');
    }

    public function testLoadThrowsExceptionIfNoHelperClassLoaded()
    {
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'not found in the registry');
        $loader = new PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        $className = $loader->load('FooBarBazBat');
    }

    public function testGetClassAfterNonStaticLoadReturnsResolvedClassName()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormSelect');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSelect'));
        $this->assertEquals('Zend\View\Helper\FormSelect', $loader->getClassName('FormSelect'));
    }

    public function testGetClassAfterStaticLoadReturnsResolvedClassName()
    {
        $this->key = 'foobar';
        $loader = new PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormCheckbox');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormCheckbox'));
        $this->assertEquals('Zend\View\Helper\FormCheckbox', $loader->getClassName('FormCheckbox'));
    }

    public function testClassFilesAreSearchedInLifoOrder()
    {
        $loader = new PluginLoader(array());
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', __DIR__ . '/_files/ZfTest');
        try {
            $className = $loader->load('FormSubmit');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSubmit'));
        $this->assertEquals('ZfTest\FormSubmit', $loader->getClassName('FormSubmit'));
    }

    /**
     * @issue ZF-2741
     */
    public function testWin32NamespacedShortNamesWillLoad()
    {
        $loader = new PluginLoader(array());
        $loader->addPrefixPath('Zend\Filter', $this->libPath . '/Zend/Filter');
        try {
            // Plugin loader will attempt to load "c:\path\to\library/Zend/Filter/Word\UnderscoreToDash.php"
            $className = $loader->load('Word\UnderscoreToDash');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('Word\UnderscoreToDash'));
    }

    /**
     * @group ZF-4670
     */
    public function testIncludeCacheShouldBeNullByDefault()
    {
        $this->assertNull(PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldAllowSpecifyingIncludeFileCache()
    {
        $cacheFile = $this->_includeCache;
        $this->testIncludeCacheShouldBeNullByDefault();
        PluginLoader::setIncludeFileCache($cacheFile);
        $this->assertEquals($cacheFile, PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldThrowExceptionWhenPathDoesNotExist()
    {
        $this->setExpectedException('Zend\Loader\PluginLoaderException', 'file does not exist');
        $cacheFile = __DIR__ . '/_filesDoNotExist/includeCache.inc.php';
        $this->testIncludeCacheShouldBeNullByDefault();
        PluginLoader::setIncludeFileCache($cacheFile);
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldAppendIncludeCacheWhenClassIsFound()
    {
        $cacheFile = $this->_includeCache;
        PluginLoader::setIncludeFileCache($cacheFile);
        $loader = new PluginLoader(array());
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', __DIR__ . '/_files/ZfTest');
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
        $loader1 = new PluginLoader(array(), "PluginLoaderStaticNamespace");
        $loader1->addPrefixPath("Zend\View\Helper", "Zend/View/Helper");

        $loader2 = new PluginLoader(array(), "PluginLoaderStaticNamespace");
        $this->assertEquals(array(
            "Zend\View\Helper\\" => array("Zend/View/Helper/"),
        ), $loader2->getPaths());
    }

    /**
     * @group ZF-4697
     */
    public function testClassFilesGrabCorrectPathForLoadedClasses()
    {
        $reflection = new \ReflectionClass('Zend\View\Helper\DeclareVars');
        $expected   = $reflection->getFileName();

        $loader = new PluginLoader(array());
        $loader->addPrefixPath('Zend\View\Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', __DIR__ . '/_files/ZfTest');
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

    /**
     * @group ZF-7350
     */
    public function testPrefixesEndingInBackslashDenoteNamespacedClasses()
    {
        $loader = new PluginLoader(array());
        $loader->addPrefixPath('Zfns\\', __DIR__ . '/_files/Zfns');
        try {
            $className = $loader->load('Foo');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zfns\\Foo', $className);
        $this->assertEquals('Zfns\\Foo', $loader->getClassName('Foo'));
    }

    /**
     * @group ZF-9721
     */
    public function testRemovePrefixPathThrowsExceptionIfPathNotRegisteredInPrefix()
    {
        try {
            $loader = new PluginLoader(array('My\Namespace\\' => 'My/Namespace/'));
            $loader->removePrefixPath('My\Namespace\\', 'ZF9721');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertType('Zend\Loader\PluginLoaderException', $e);
            $this->assertContains('Prefix My\Namespace\ / Path ZF9721', $e->getMessage());
        }
        $this->assertEquals(1, count($loader->getPaths('My\Namespace\\')));
    }
}
