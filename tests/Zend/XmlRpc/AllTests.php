<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/XmlRpc/ValueTest.php';
require_once 'Zend/XmlRpc/RequestTest.php';
require_once 'Zend/XmlRpc/Request/HttpTest.php';
require_once 'Zend/XmlRpc/ResponseTest.php';
require_once 'Zend/XmlRpc/FaultTest.php';
require_once 'Zend/XmlRpc/ClientTest.php';
require_once 'Zend/XmlRpc/ServerTest.php';
require_once 'Zend/XmlRpc/Server/CacheTest.php';
require_once 'Zend/XmlRpc/Server/FaultTest.php';

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
        $suite->addTestSuite('Zend_XmlRpc_Server_CacheTest');
        $suite->addTestSuite('Zend_XmlRpc_Server_FaultTest');
       
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_XmlRpc_AllTests::main') {
    Zend_XmlRpc_AllTests::main();
}
