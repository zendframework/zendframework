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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Cache_AllTests::main');
}

require_once 'Zend/Cache/FactoryTest.php';
require_once 'Zend/Cache/CoreTest.php';
require_once 'Zend/Cache/FileBackendTest.php';
require_once 'Zend/Cache/SqliteBackendTest.php';
require_once 'Zend/Cache/OutputFrontendTest.php';
require_once 'Zend/Cache/FunctionFrontendTest.php';
require_once 'Zend/Cache/ClassFrontendTest.php';
require_once 'Zend/Cache/FileFrontendTest.php';
require_once 'Zend/Cache/ApcBackendTest.php';
require_once 'Zend/Cache/XcacheBackendTest.php';
require_once 'Zend/Cache/MemcachedBackendTest.php';
require_once 'Zend/Cache/PageFrontendTest.php';
require_once 'Zend/Cache/ZendPlatformBackendTest.php';
require_once 'Zend/Cache/SkipTests.php';
require_once 'Zend/Cache/TwoLevelsBackendTest.php';
require_once 'Zend/Cache/ZendServerDiskTest.php';
require_once 'Zend/Cache/ZendServerShMemTest.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Cache');

        $suite->addTestSuite('Zend_Cache_FactoryTest');
        $suite->addTestSuite('Zend_Cache_CoreTest');
        $suite->addTestSuite('Zend_Cache_FileBackendTest');
        $suite->addTestSuite('Zend_Cache_OutputFrontendTest');
        $suite->addTestSuite('Zend_Cache_FunctionFrontendTest');
        $suite->addTestSuite('Zend_Cache_ClassFrontendTest');
        $suite->addTestSuite('Zend_Cache_FileFrontendTest');
        $suite->addTestSuite('Zend_Cache_PageFrontendTest');

        /*
         * Check if SQLite tests are enabled, and if extension and driver are available.
         */
        if (!defined('TESTS_ZEND_CACHE_SQLITE_ENABLED') ||
            constant('TESTS_ZEND_CACHE_SQLITE_ENABLED') === false) {
            $skipTest = new Zend_Cache_SqliteBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!extension_loaded('sqlite')) {
            $skipTest = new Zend_Cache_SqliteBackendTest_SkipTests();
            $skipTest->message = "Extension 'sqlite' is not loaded";
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_SqliteBackendTest');
        }

        /*
         * Check if APC tests are enabled, and if extension is available.
         */
        if (!defined('TESTS_ZEND_CACHE_APC_ENABLED') ||
            constant('TESTS_ZEND_CACHE_APC_ENABLED') === false) {
            $skipTest = new Zend_Cache_ApcBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!extension_loaded('apc')) {
            $skipTest = new Zend_Cache_ApcBackendTest_SkipTests();
            $skipTest->message = "Extension 'APC' is not loaded";
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_ApcBackendTest');
        }

        /*
         * Check if Xcache tests are enabled, and if extension is available.
         */
        if (!defined('TESTS_ZEND_CACHE_XCACHE_ENABLED') ||
            constant('TESTS_ZEND_CACHE_XCACHE_ENABLED') === false) {
            $skipTest = new Zend_Cache_XCacheBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!extension_loaded('xcache')) {
            $skipTest = new Zend_Cache_XCacheBackendTest_SkipTests();
            $skipTest->message = "Extension 'XCache' is not loaded";
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_XCacheBackendTest');
        }

        /*
         * Check if Memcached tests are enabled, and if extension is available.
         */
        if (!defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') ||
            constant('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') === false) {
            $skipTest = new Zend_Cache_MemcachedBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!extension_loaded('memcache')) {
            $skipTest = new Zend_Cache_MemcachedBackendTest_SkipTests();
            $skipTest->message = "Extension 'APC' is not loaded";
            $suite->addTest($skipTest);
        } else {
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_HOST', '127.0.0.1');
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PORT', 11211);
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT', true);
            }
            $suite->addTestSuite('Zend_Cache_MemcachedBackendTest');
        }

        /*
         * Check if Zend Platform tests are enabled, and if extension is available.
         */
        if (!defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') ||
            constant('TESTS_ZEND_CACHE_PLATFORM_ENABLED') === false) {
            $skipTest = new Zend_Cache_ZendPlatformBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!function_exists('accelerator_license_info')) {
            $skipTest = new Zend_Cache_ZendPlatformBackendTest_SkipTests();
            $skipTest->message = 'Extension for Zend Platform is not loaded';
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_ZendPlatformBackendTest');
        }

        /*
         * Check if APC tests are enabled, and if extension is available.
         */
        if (!defined('TESTS_ZEND_CACHE_APC_ENABLED') ||
            constant('TESTS_ZEND_CACHE_APC_ENABLED') === false) {
            $skipTest = new Zend_Cache_TwoLevelsBackendTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!extension_loaded('apc')) {
            $skipTest = new Zend_Cache_TwoLevelsBackendTest_SkipTests();
            $skipTest->message = "Extension 'APC' is not loaded";
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_TwoLevelsBackendTest');
        }

        /*
         * Check if Zend Server tests are enabled, and appropriate functions are available.
         */
        if (!defined('TESTS_ZEND_CACHE_ZENDSERVER_ENABLED') ||
            constant('TESTS_ZEND_CACHE_ZENDSERVER_ENABLED') === false) {
            $skipTest = new Zend_Cache_ZendServerTest_SkipTests();
            $skipTest->message = 'Tests are not enabled in TestConfiguration.php';
            $suite->addTest($skipTest);
        } else if (!function_exists('zend_shm_cache_store')) {
            $skipTest = new Zend_Cache_ZendServerTest_SkipTests();
            $skipTest->message = "Zend Server caching environment is not available";
            $suite->addTest($skipTest);
        } else {
            $suite->addTestSuite('Zend_Cache_ZendServerDiskTest');
            $suite->addTestSuite('Zend_Cache_ZendServerShMemTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cache_AllTests::main') {
    Zend_Cache_AllTests::main();
}
