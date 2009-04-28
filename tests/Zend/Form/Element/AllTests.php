<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_Element_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/Element/ButtonTest.php';
require_once 'Zend/Form/Element/CaptchaTest.php';
require_once 'Zend/Form/Element/CheckboxTest.php';
require_once 'Zend/Form/Element/FileTest.php';
require_once 'Zend/Form/Element/HashTest.php';
require_once 'Zend/Form/Element/HiddenTest.php';
require_once 'Zend/Form/Element/ImageTest.php';
require_once 'Zend/Form/Element/MultiCheckboxTest.php';
require_once 'Zend/Form/Element/MultiselectTest.php';
require_once 'Zend/Form/Element/PasswordTest.php';
require_once 'Zend/Form/Element/RadioTest.php';
require_once 'Zend/Form/Element/ResetTest.php';
require_once 'Zend/Form/Element/SelectTest.php';
require_once 'Zend/Form/Element/SubmitTest.php';
require_once 'Zend/Form/Element/TextareaTest.php';
require_once 'Zend/Form/Element/TextTest.php';

class Zend_Form_Element_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Form_Element');

        $suite->addTestSuite('Zend_Form_Element_ButtonTest');
        $suite->addTestSuite('Zend_Form_Element_CaptchaTest');
        $suite->addTestSuite('Zend_Form_Element_CheckboxTest');
        $suite->addTestSuite('Zend_Form_Element_FileTest');
        $suite->addTestSuite('Zend_Form_Element_HashTest');
        $suite->addTestSuite('Zend_Form_Element_HiddenTest');
        $suite->addTestSuite('Zend_Form_Element_ImageTest');
        $suite->addTestSuite('Zend_Form_Element_MultiCheckboxTest');
        $suite->addTestSuite('Zend_Form_Element_MultiselectTest');
        $suite->addTestSuite('Zend_Form_Element_PasswordTest');
        $suite->addTestSuite('Zend_Form_Element_RadioTest');
        $suite->addTestSuite('Zend_Form_Element_ResetTest');
        $suite->addTestSuite('Zend_Form_Element_SelectTest');
        $suite->addTestSuite('Zend_Form_Element_SubmitTest');
        $suite->addTestSuite('Zend_Form_Element_TextareaTest');
        $suite->addTestSuite('Zend_Form_Element_TextTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_Element_AllTests::main') {
    Zend_Form_Element_AllTests::main();
}
