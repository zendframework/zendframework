<?php
/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_AllTests::main');
}

require_once 'Zend/Amf/Adobe/IntrospectorTest.php';
require_once 'Zend/Amf/RequestTest.php';
require_once 'Zend/Amf/ResponseTest.php';
require_once 'Zend/Amf/ServerTest.php';
require_once 'Zend/Amf/TypeLoaderTest.php';
require_once 'Zend/Amf/Util/BinaryStreamTest.php';
require_once 'Zend/Amf/Value/MessageBodyTest.php';
require_once 'Zend/Amf/Value/MessageHeaderTest.php';
require_once 'Zend/Amf/AuthTest.php';
require_once 'Zend/Amf/ResourceTest.php';


class Zend_Amf_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Amf');

        $suite->addTestSuite('Zend_Amf_Adobe_IntrospectorTest');
        $suite->addTestSuite('Zend_Amf_RequestTest');
        $suite->addTestSuite('Zend_Amf_ResponseTest');
        $suite->addTestSuite('Zend_Amf_ServerTest');
        $suite->addTestSuite('Zend_Amf_TypeLoaderTest');
        $suite->addTestSuite('Zend_Amf_Util_BinaryStreamTest');
        $suite->addTestSuite('Zend_Amf_Value_MessageBodyTest');
        $suite->addTestSuite('Zend_Amf_Value_MessageHeaderTest');
        $suite->addTestSuite('Zend_Amf_AuthTest');
        $suite->addTestSuite('Zend_Amf_ResourceTest');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_AllTests::main') {
    Zend_Amf_AllTests::main();
}
