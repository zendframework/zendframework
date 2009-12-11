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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_AllTests::main');
}

require_once 'Zend/XmlRpc/ValueTest.php';
require_once 'Zend/XmlRpc/RequestTest.php';
require_once 'Zend/XmlRpc/Request/HttpTest.php';
require_once 'Zend/XmlRpc/ResponseTest.php';
require_once 'Zend/XmlRpc/FaultTest.php';
require_once 'Zend/XmlRpc/ClientTest.php';
require_once 'Zend/XmlRpc/ServerTest.php';
require_once 'Zend/XmlRpc/GeneratorTest.php';
require_once 'Zend/XmlRpc/Server/CacheTest.php';
require_once 'Zend/XmlRpc/Server/FaultTest.php';

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_XmlRpc');

        $suite->addTestSuite('Zend_XmlRpc_ValueTest');
        $suite->addTestSuite('Zend_XmlRpc_RequestTest');
        $suite->addTestSuite('Zend_XmlRpc_Request_HttpTest');
        $suite->addTestSuite('Zend_XmlRpc_ResponseTest');
        $suite->addTestSuite('Zend_XmlRpc_FaultTest');
        $suite->addTestSuite('Zend_XmlRpc_ClientTest');
        $suite->addTestSuite('Zend_XmlRpc_ServerTest');
        $suite->addTestSuite('Zend_XmlRpc_GeneratorTest');
        $suite->addTestSuite('Zend_XmlRpc_Server_CacheTest');
        $suite->addTestSuite('Zend_XmlRpc_Server_FaultTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_XmlRpc_AllTests::main') {
    Zend_XmlRpc_AllTests::main();
}
