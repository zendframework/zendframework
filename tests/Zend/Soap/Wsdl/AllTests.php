<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "ArrayOfTypeComplexStrategyTest.php";
require_once "ArrayOfTypeSequenceStrategyTest.php";
require_once "DefaultComplexTypeTest.php";

class Zend_Soap_Wsdl_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Soap_Wsdl');

        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_DefaultComplexTypeTest');

        return $suite;
    }
}