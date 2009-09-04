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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Loader_AutoloaderMultiVersionTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class Zend_Loader_AutoloaderMultiVersionTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_ENABLED')) {
            $this->markTestSkipped();
        }

        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        Zend_Loader_Autoloader::resetInstance();
        $this->path        = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_PATH');
        $this->latest      = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST');
        $this->latestMajor = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST_MAJOR');
        $this->latestMinor = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST_MINOR');
        $this->specific    = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_SPECIFIC');
        $this->autoloader  = Zend_Loader_Autoloader::getInstance();
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Retore original include_path
        set_include_path($this->includePath);

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testZfPathIsNullByDefault()
    {
        $this->assertNull($this->autoloader->getZfPath());
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testSettingZfPathFailsOnInvalidVersionString()
    {
        $this->autoloader->setZfPath($this->path, 'foo.bar.baz.bat');
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testSettingZfPathFailsWhenBasePathDoesNotExist()
    {
        $this->autoloader->setZfPath('foo.bar.baz.bat', 'latest');
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testSettingZfVersionFailsWhenNoValidInstallsDiscovered()
    {
        $this->autoloader->setZfPath(dirname(__FILE__), 'latest');
    }

    public function testAutoloadLatestUsesLatestVersion()
    {
        $this->autoloader->setZfPath($this->path, 'latest');
        $actual = $this->autoloader->getZfPath();
        $this->assertContains($this->latest, $actual);
    }

    public function testAutoloadLatestIncludesLibraryInPath()
    {
        $this->autoloader->setZfPath($this->path, 'latest');
        $actual = $this->autoloader->getZfPath();
        $this->assertRegexp('#' . preg_quote($this->latest) . '[^/\\\]*/library#', $actual);
    }

    public function testAutoloadLatestAddsPathToIncludePath()
    {
        $this->autoloader->setZfPath($this->path, 'latest');
        $incPath = get_include_path();
        $this->assertRegexp('#' . preg_quote($this->latest) . '[^/\\\]*/library#', $incPath);
    }

    public function testAutoloadMajorRevisionShouldUseLatestFromMajorRevision()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMajor, 'major'));
        $actual = $this->autoloader->getZfPath();
        $this->assertContains($this->latestMajor, $actual);
    }

    public function testAutoloadMajorRevisionIncludesLibraryInPath()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMajor, 'major'));
        $actual = $this->autoloader->getZfPath();
        $this->assertRegexp('#' . preg_quote($this->latestMajor) . '[^/\\\]*/library#', $actual);
    }

    public function testAutoloadMajorRevisionAddsPathToIncludePath()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMajor, 'major'));
        $incPath = get_include_path();
        $this->assertRegexp('#' . preg_quote($this->latestMajor) . '[^/\\\]*/library#', $incPath);
    }

    public function testAutoloadMinorRevisionShouldUseLatestFromMinorRevision()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMinor, 'minor'));
        $actual = $this->autoloader->getZfPath();
        $this->assertContains($this->latestMinor, $actual);
    }

    public function testAutoloadMinorRevisionIncludesLibraryInPath()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMinor, 'minor'));
        $actual = $this->autoloader->getZfPath();
        $this->assertRegexp('#' . preg_quote($this->latestMinor) . '[^/\\\]*/library#', $actual);
    }

    public function testAutoloadMinorRevisionAddsPathToIncludePath()
    {
        $this->autoloader->setZfPath($this->path, $this->_getVersion($this->latestMinor, 'minor'));
        $incPath = get_include_path();
        $this->assertRegexp('#' . preg_quote($this->latestMinor) . '[^/\\\]*/library#', $incPath);
    }

    public function testAutoloadSpecificRevisionShouldUseThatVersion()
    {
        $this->autoloader->setZfPath($this->path, $this->specific);
        $actual = $this->autoloader->getZfPath();
        $this->assertContains($this->specific, $actual);
    }

    public function testAutoloadSpecificRevisionIncludesLibraryInPath()
    {
        $this->autoloader->setZfPath($this->path, $this->specific);
        $actual = $this->autoloader->getZfPath();
        $this->assertRegexp('#' . preg_quote($this->specific) . '[^/\\\]*/library#', $actual);
    }

    public function testAutoloadSpecificRevisionAddsPathToIncludePath()
    {
        $this->autoloader->setZfPath($this->path, $this->specific);
        $incPath = get_include_path();
        $this->assertRegexp('#' . preg_quote($this->specific) . '[^/\\\]*/library#', $incPath);
    }

    protected function _getVersion($version, $type)
    {
        $parts = explode('.', $version);
        switch ($type) {
            case 'major': 
                $value = array_shift($parts);
                break;
            case 'minor':
                $value  = array_shift($parts);
                $value .= '.' . array_shift($parts);
                break;
        }
        return $value;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Loader_AutoloaderMultiVersionTest::main') {
    Zend_Loader_AutoloaderMultiVersionTest::main();
}
