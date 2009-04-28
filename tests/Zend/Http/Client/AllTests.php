<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_Client_AllTests::main');
}

// Read local configuration
if (! defined('TESTS_ZEND_HTTP_CLIENT_BASEURI') &&
    is_readable('TestConfiguration.php')) {

    require_once 'TestConfiguration.php';
}

require_once realpath(dirname(__FILE__) . '/../../../') . '/TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Http/Client/StaticTest.php';
require_once 'Zend/Http/Client/SocketTest.php';
require_once 'Zend/Http/Client/SocketKeepaliveTest.php';
require_once 'Zend/Http/Client/SocketPersistentTest.php';
require_once 'Zend/Http/Client/TestAdapterTest.php';
require_once 'Zend/Http/Client/ProxyAdapterTest.php';
require_once 'Zend/Http/Client/SkipTests.php';
require_once 'Zend/Http/Client/CurlTest.php';

class Zend_Http_Client_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_Http_Client_StaticTest');
        if (defined('TESTS_ZEND_HTTP_CLIENT_BASEURI') && Zend_Uri_Http::check(TESTS_ZEND_HTTP_CLIENT_BASEURI)) {
            $suite->addTestSuite('Zend_Http_Client_SocketTest');
            $suite->addTestSuite('Zend_Http_Client_SocketKeepaliveTest');
            $suite->addTestSuite('Zend_Http_Client_SocketPersistentTest');
        } else {
            $suite->addTestSuite('Zend_Http_Client_Skip_SocketTest');
        }
        $suite->addTestSuite('Zend_Http_Client_TestAdapterTest');
        if (defined('TESTS_ZEND_HTTP_CLIENT_HTTP_PROXY') && TESTS_ZEND_HTTP_CLIENT_HTTP_PROXY) {
            $suite->addTestSuite('Zend_Http_Client_ProxyAdapterTest');
        } else {
            $suite->addTestSuite('Zend_Http_Client_Skip_ProxyAdapterTest');
        }
        $suite->addTestSuite('Zend_Http_Client_CurlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_Client_AllTests::main') {
    Zend_Http_Client_AllTests::main();
}
