<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/Decorator/AllTests.php';
require_once 'Zend/Form/DisplayGroupTest.php';
require_once 'Zend/Form/ElementTest.php';
require_once 'Zend/Form/Element/AllTests.php';
require_once 'Zend/Form/FormTest.php';

class Zend_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Form');

        $suite->addTest(Zend_Form_Decorator_AllTests::suite());
        $suite->addTestSuite('Zend_Form_DisplayGroupTest');
        $suite->addTestSuite('Zend_Form_ElementTest');
        $suite->addTest(Zend_Form_Element_AllTests::suite());
        $suite->addTestSuite('Zend_Form_FormTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_AllTests::main') {
    Zend_Form_AllTests::main();
}
