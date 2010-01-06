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
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_Client_AllTests::main');
}

require_once 'Zend/Http/Client/StaticTest.php';
require_once 'Zend/Http/Client/SocketTest.php';
require_once 'Zend/Http/Client/SocketKeepaliveTest.php';
require_once 'Zend/Http/Client/SocketPersistentTest.php';
require_once 'Zend/Http/Client/TestAdapterTest.php';
require_once 'Zend/Http/Client/ProxyAdapterTest.php';
require_once 'Zend/Http/Client/SkipTests.php';
require_once 'Zend/Http/Client/CurlTest.php';

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Http_Client');

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
