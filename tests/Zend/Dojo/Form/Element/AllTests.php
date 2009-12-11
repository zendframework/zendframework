<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_Form_Element_AllTests::main');
}

require_once 'Zend/Dojo/Form/Element/CheckBoxTest.php';
require_once 'Zend/Dojo/Form/Element/ComboBoxTest.php';
require_once 'Zend/Dojo/Form/Element/CurrencyTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/DateTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/DijitTest.php';
require_once 'Zend/Dojo/Form/Element/EditorTest.php';
require_once 'Zend/Dojo/Form/Element/FilteringSelectTest.php';
require_once 'Zend/Dojo/Form/Element/HorizontalSliderTest.php';
require_once 'Zend/Dojo/Form/Element/NumberSpinnerTest.php';
require_once 'Zend/Dojo/Form/Element/NumberTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/PasswordTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/RadioButtonTest.php';
require_once 'Zend/Dojo/Form/Element/SimpleTextareaTest.php';
require_once 'Zend/Dojo/Form/Element/SubmitButtonTest.php';
require_once 'Zend/Dojo/Form/Element/TextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/TextareaTest.php';
require_once 'Zend/Dojo/Form/Element/TimeTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/ValidationTextBoxTest.php';
require_once 'Zend/Dojo/Form/Element/VerticalSliderTest.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo_Form_Element');

        $suite->addTestSuite('Zend_Dojo_Form_Element_CheckBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_ComboBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_CurrencyTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_DateTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_DijitTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_EditorTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_FilteringSelectTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_HorizontalSliderTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_NumberSpinnerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_NumberTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_PasswordTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_RadioButtonTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_SimpleTextareaTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_SubmitButtonTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_TextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_TextareaTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_TimeTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_ValidationTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_Form_Element_VerticalSliderTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_Form_Element_AllTests::main') {
    Zend_Dojo_Form_Element_AllTests::main();
}
