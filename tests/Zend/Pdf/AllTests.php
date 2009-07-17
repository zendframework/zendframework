<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Pdf_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Pdf/ActionTest.php';
require_once 'Zend/Pdf/DestinationTest.php';
require_once 'Zend/Pdf/DrawingTest.php';
require_once 'Zend/Pdf/FactoryTest.php';
require_once 'Zend/Pdf/NamedDestinationsTest.php';
require_once 'Zend/Pdf/ProcessingTest.php';

require_once 'Zend/Pdf/Element/AllTests.php';

class Zend_Pdf_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Pdf');

        $suite->addTestSuite('Zend_Pdf_ActionTest');
        $suite->addTestSuite('Zend_Pdf_DestinationTest');
        $suite->addTestSuite('Zend_Pdf_DrawingTest');
        $suite->addTestSuite('Zend_Pdf_FactoryTest');
        $suite->addTestSuite('Zend_Pdf_NamedDestinationsTest');
        $suite->addTestSuite('Zend_Pdf_ProcessingTest');

        $suite->addTest(Zend_Pdf_Element_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Pdf_AllTests::main') {
    Zend_Pdf_AllTests::main();
}
