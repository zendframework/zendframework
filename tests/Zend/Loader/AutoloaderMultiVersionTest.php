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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\Autoloader;

require_once "PHPUnit/Framework/Error/Notice.php";
require_once "PHPUnit/Framework/TestFailure.php";

/**
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class AutoloaderMultiVersionTest extends \PHPUnit_Framework_TestCase
{
    protected function isEnabled()
    {
        return (bool)constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_ENABLED');
    }
    
    public function setUp()
    {
        if (!$this->isEnabled()) {
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

        Autoloader::resetInstance();
        $this->path        = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_PATH');
        $this->latest      = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST');
        $this->latestMajor = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST_MAJOR');
        $this->latestMinor = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST_MINOR');
        $this->specific    = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_SPECIFIC');
        $this->autoloader  = Autoloader::getInstance();
    }

    public function tearDown()
    {
        if (!$this->isEnabled()) {
            return;
        }
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
        Autoloader::resetInstance();
        Autoloader::getInstance();
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
        $this->autoloader->setZfPath(__DIR__, 'latest');
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
