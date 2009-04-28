<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_Decorator_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/Decorator/AbstractTest.php';
require_once 'Zend/Form/Decorator/CallbackTest.php';
require_once 'Zend/Form/Decorator/DescriptionTest.php';
require_once 'Zend/Form/Decorator/ErrorsTest.php';
require_once 'Zend/Form/Decorator/FieldsetTest.php';
require_once 'Zend/Form/Decorator/FileTest.php';
require_once 'Zend/Form/Decorator/FormTest.php';
require_once 'Zend/Form/Decorator/HtmlTagTest.php';
require_once 'Zend/Form/Decorator/ImageTest.php';
require_once 'Zend/Form/Decorator/LabelTest.php';
require_once 'Zend/Form/Decorator/ViewHelperTest.php';
require_once 'Zend/Form/Decorator/ViewScriptTest.php';

class Zend_Form_Decorator_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Form_Decorator');

        $suite->addTestSuite('Zend_Form_Decorator_AbstractTest');
        $suite->addTestSuite('Zend_Form_Decorator_CallbackTest');
        $suite->addTestSuite('Zend_Form_Decorator_DescriptionTest');
        $suite->addTestSuite('Zend_Form_Decorator_ErrorsTest');
        $suite->addTestSuite('Zend_Form_Decorator_FieldsetTest');
        $suite->addTestSuite('Zend_Form_Decorator_FileTest');
        $suite->addTestSuite('Zend_Form_Decorator_FormTest');
        $suite->addTestSuite('Zend_Form_Decorator_HtmlTagTest');
        $suite->addTestSuite('Zend_Form_Decorator_ImageTest');
        $suite->addTestSuite('Zend_Form_Decorator_LabelTest');
        $suite->addTestSuite('Zend_Form_Decorator_ViewHelperTest');
        $suite->addTestSuite('Zend_Form_Decorator_ViewScriptTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_Decorator_AllTests::main') {
    Zend_Form_Decorator_AllTests::main();
}
