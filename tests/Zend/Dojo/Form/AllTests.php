<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_Form_AllTests::main');
}
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Dojo/Form/Decorator/AllTests.php';
require_once 'Zend/Dojo/Form/Element/AllTests.php';
require_once 'Zend/Dojo/Form/FormTest.php';
require_once 'Zend/Dojo/Form/SubFormTest.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo_Form');

        $suite->addTest(Zend_Dojo_Form_Decorator_AllTests::suite());
        $suite->addTest(Zend_Dojo_Form_Element_AllTests::suite());
        $suite->addTestSuite('Zend_Dojo_Form_FormTest');
        $suite->addTestSuite('Zend_Dojo_Form_SubFormTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_Form_AllTests::main') {
    Zend_Dojo_Form_AllTests::main();
}
