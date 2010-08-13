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

namespace ZendTest;
use \stdClass,
    \Phar,
    \Zend\Loader,
    \Zend\Loader\Autoloader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
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

        $this->error = null;
        $this->errorHandler = null;
        Autoloader::resetInstance();
    }

    public function tearDown()
    {
        if ($this->errorHandler !== null) {
            restore_error_handler();
        }

        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        if (is_array($this->loaders)) {
            foreach ($this->loaders as $loader) {
                spl_autoload_register($loader);
            }
        }

        // Retore original include_path
        set_include_path($this->includePath);

        // Reset autoloader instance so it doesn't affect other tests
        Autoloader::resetInstance();
    }

    public function setErrorHandler()
    {
        set_error_handler(array($this, 'handleErrors'), E_USER_NOTICE);
        $this->errorHandler = true;
    }

    public function handleErrors($errno, $errstr)
    {
        $this->error = $errstr;
    }

    /**
     * Tests that a class can be loaded from a well-formed PHP file
     */
    public function testLoaderClassValid()
    {
        $dir = implode(array(__DIR__, '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        Loader::loadClass('Class1', $dir);
    }

    public function testLoaderInterfaceViaLoadClass()
    {
        try {
            Loader::loadClass('Zend\Controller\Dispatcher');
        } catch (\Zend_Exception $e) {
            $this->fail('Loading interfaces should not fail');
        }
    }

    public function testLoaderLoadClassWithDotDir()
    {
        $dirs = array('.');
        try {
            Loader::loadClass('\\Zend\\Version', $dirs);
        } catch (\Zend_Exception $e) {
            $this->fail('Loading from dot should not fail');
        }
    }

    /**
     * Tests that an exception is thrown when a file is loaded but the
     * class is not found within the file
     */
    public function testLoaderClassNonexistent()
    {
        $dir = implode(array(__DIR__, '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        $this->setExpectedException('\\Zend\\Loader\\ClassNotFoundException');
        Loader::loadClass('ClassNonexistent', $dir);
    }

    /**
     * Tests that an exception is thrown if the $dirs argument is
     * not a string or an array.
     */
    public function testLoaderInvalidDirs()
    {
        $this->setExpectedException('\\Zend\\Loader\\InvalidDirectoryArgumentException');
        Loader::loadClass('Zend_Invalid_Dirs', new stdClass());
    }

    /**
     * Tests that a class can be loaded from the search directories.
     */
    public function testLoaderClassSearchDirs()
    {
        $dirs = array();
        foreach (array('_testDir1', '_testDir2') as $dir) {
            $dirs[] = implode(array(__DIR__, '_files', $dir), DIRECTORY_SEPARATOR);
        }

        // throws exception on failure
        Loader::loadClass('Class1', $dirs);
        Loader::loadClass('Class2', $dirs);
    }

    /**
     * Tests that a class locatedin a subdirectory can be loaded from the search directories
     */
    public function testLoaderClassSearchSubDirs()
    {
        $dirs = array();
        foreach (array('_testDir1', '_testDir2') as $dir) {
            $dirs[] = implode(array(__DIR__, '_files', $dir), DIRECTORY_SEPARATOR);
        }

        // throws exception on failure
        Loader::loadClass('Class1_Subclass2', $dirs);
    }

    /**
     * Tests that the security filter catches illegal characters.
     */
    public function testLoaderClassIllegalFilename()
    {
        $this->setExpectedException('\\Zend\\Loader\\SecurityException', 'Illegal character');
        Loader::loadClass('/path/:to/@danger');
    }

    /**
     * Tests that loadFile() finds a file in the include_path when $dirs is null
     */
    public function testLoaderFileIncludePathEmptyDirs()
    {
        $saveIncludePath = get_include_path();
        set_include_path(implode(array($saveIncludePath, implode(array(__DIR__, '_files', '_testDir1'), DIRECTORY_SEPARATOR)), PATH_SEPARATOR));

        $this->assertTrue(Loader::loadFile('Class3.php', null));

        set_include_path($saveIncludePath);
    }

    /**
     * Tests that loadFile() finds a file in the include_path when $dirs is non-null
     * This was not working vis-a-vis ZF-1174
     */
    public function testLoaderFileIncludePathNonEmptyDirs()
    {
        $saveIncludePath = get_include_path();
        set_include_path(implode(array($saveIncludePath, implode(array(__DIR__, '_files', '_testDir1'), DIRECTORY_SEPARATOR)), PATH_SEPARATOR));

        $this->assertTrue(Loader::loadFile('Class4.php', implode(PATH_SEPARATOR, array('foo', 'bar'))));

        set_include_path($saveIncludePath);
    }

    /**
     * Tests that isReadable works
     */
    public function testLoaderIsReadable()
    {
        $this->assertTrue(Loader::isReadable(__FILE__));
        $this->assertFalse(Loader::isReadable(__FILE__ . '.foobaar'));

        // test that a file in include_path gets loaded, see ZF-2985
        $this->assertTrue(Loader::isReadable('Zend/Controller/Front.php'), get_include_path());
    }

    public function testLoaderRegisterAutoloadFailsWithoutSplAutoload()
    {
        if (function_exists('spl_autoload_register')) {
            $this->markTestSkipped("spl_autoload() is installed on this PHP installation; cannot test for failure");
        }

        try {
            Loader::registerAutoload();
            $this->fail('registerAutoload should fail without spl_autoload');
        } catch (Zend_Exception $e) {
        }
    }

    /**
     * @group ZF-8200
     */
    public function testLoadClassShouldAllowLoadingPhpNamespacedClasses()
    {
        Loader::loadClass('\Zfns\Foo', array(__DIR__ . '/_files'));
    }

    /**
     * @group ZF-7271
     * @group ZF-8913
     */
    public function testIsReadableShouldHonorStreamDefinitions()
    {
        $pharFile = __DIR__ . '/_files/Zend_LoaderTest.phar';
        $phar     = new Phar($pharFile, 0, 'zlt.phar');
        $incPath = 'phar://zlt.phar'
                 . PATH_SEPARATOR . $this->includePath;
        set_include_path($incPath);
        $this->assertTrue(Loader::isReadable('User.php'));
        unset($phar);
    }

    /**
     * @group ZF-8913
     */
    public function testIsReadableShouldNotLockWhenTestingForNonExistantFileInPhar()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped();
        }

        $pharFile = __DIR__ . '/_files/LoaderTest.phar';
        $phar     = new Phar($pharFile, 0, 'zlt.phar');
        $incPath = 'phar://zlt.phar'
                 . PATH_SEPARATOR . $this->includePath;
        set_include_path($incPath);
        $this->assertFalse(Loader::isReadable('does-not-exist'));
        unset($phar);
    }

    /**
     * @group ZF-7271
     */
    public function testExplodeIncludePathProperlyIdentifiesStreamSchemes()
    {
        if (PATH_SEPARATOR != ':') {
            $this->markTestSkipped();
        }
        $path = 'phar://zlt.phar:/var/www:.:filter://[a-z]:glob://*';
        $paths = Loader::explodeIncludePath($path);
        $this->assertSame(array(
            'phar://zlt.phar',
            '/var/www',
            '.',
            'filter://[a-z]',
            'glob://*',
        ), $paths);
    }

    /**
     * @group ZF-9100
     */
    public function testIsReadableShouldReturnTrueForAbsolutePaths()
    {
        set_include_path(__DIR__ . '../../../');
        $path = __DIR__;
        $this->assertTrue(Loader::isReadable($path));
    }

    /**
     * @group ZF-9263
     * @group ZF-9166
     * @group ZF-9306
     */
    public function testIsReadableShouldFailEarlyWhenProvidedInvalidWindowsAbsolutePath()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
            $this->markTestSkipped('Windows-only test');
        }
        $path = 'C:/this/file/should/not/exist.php';
        $this->assertFalse(Loader::isReadable($path));
    }
}
