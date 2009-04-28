<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Soap_AllTests::main');
}

require_once dirname(__FILE__)."/../../TestHelper.php";

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Soap/ClientTest.php';
require_once 'Zend/Soap/ServerTest.php';
require_once 'Zend/Soap/WsdlTest.php';
require_once "Zend/Soap/Wsdl/ArrayOfTypeComplexStrategyTest.php";
require_once "Zend/Soap/Wsdl/ArrayOfTypeSequenceStrategyTest.php";
require_once 'Zend/Soap/AutoDiscoverTest.php';
require_once 'Zend/Soap/AutoDiscover/OnlineTest.php';

class Zend_Soap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Soap');

        $suite->addTestSuite('Zend_Soap_ClientTest');
        $suite->addTestSuite('Zend_Soap_ServerTest');
        $suite->addTestSuite('Zend_Soap_WsdlTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest');
        $suite->addTestSuite('Zend_Soap_AutoDiscoverTest');
        $suite->addTestSuite('Zend_Soap_AutoDiscover_OnlineTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Soap_AllTests::main') {
    Zend_Soap_AllTests::main();
}
