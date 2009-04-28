<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Pdf_Element_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Pdf/Element/ArrayTest.php';
require_once 'Zend/Pdf/Element/BooleanTest.php';
require_once 'Zend/Pdf/Element/DictionaryTest.php';
require_once 'Zend/Pdf/Element/NameTest.php';
require_once 'Zend/Pdf/Element/NullTest.php';
require_once 'Zend/Pdf/Element/NumericTest.php';
require_once 'Zend/Pdf/Element/ObjectTest.php';
require_once 'Zend/Pdf/Element/Object/AllTests.php';
require_once 'Zend/Pdf/Element/StreamTest.php';
require_once 'Zend/Pdf/Element/StringTest.php';
require_once 'Zend/Pdf/Element/String/AllTests.php';

class Zend_Pdf_Element_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Pdf_Element');

        $suite->addTestSuite('Zend_Pdf_Element_ArrayTest');
        $suite->addTestSuite('Zend_Pdf_Element_BooleanTest');
        $suite->addTestSuite('Zend_Pdf_Element_DictionaryTest');
        $suite->addTestSuite('Zend_Pdf_Element_NameTest');
        $suite->addTestSuite('Zend_Pdf_Element_NullTest');
        $suite->addTestSuite('Zend_Pdf_Element_NumericTest');
        $suite->addTestSuite('Zend_Pdf_Element_ObjectTest');
        $suite->addTest(Zend_Pdf_Element_Object_AllTests::suite());
        $suite->addTestSuite('Zend_Pdf_Element_StreamTest');
        $suite->addTestSuite('Zend_Pdf_Element_StringTest');
        $suite->addTest(Zend_Pdf_Element_String_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Pdf_Element_AllTests::main') {
    Zend_Pdf_Element_AllTests::main();
}
