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
use \stdClass,
    \Phar,
    \Zend\Loader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Zend_Loader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
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
        $this->assertTrue(Loader::isReadable('Zend/Version.php'), get_include_path());
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
        if (ini_get('phar.readonly')) {
            $this->markTestSkipped(
                "creating phar archive is disabled by the php.ini setting 'phar.readonly'"
            );
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
