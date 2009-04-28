<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Pdf_Element_String_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Pdf/Element/String/BinaryTest.php';

class Zend_Pdf_Element_String_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Pdf_Element_String');

        $suite->addTestSuite('Zend_Pdf_Element_String_BinaryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Pdf_Element_String_AllTests::main') {
    Zend_Pdf_Element_String_AllTests::main();
}
