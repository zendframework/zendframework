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

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_View_Helper_AllTests::main');
}

require_once 'Zend/Dojo/View/Helper/DojoTest.php';
require_once 'Zend/Dojo/View/Helper/AccordionContainerTest.php';
require_once 'Zend/Dojo/View/Helper/AccordionPaneTest.php';
require_once 'Zend/Dojo/View/Helper/BorderContainerTest.php';
require_once 'Zend/Dojo/View/Helper/ButtonTest.php';
require_once 'Zend/Dojo/View/Helper/CheckBoxTest.php';
require_once 'Zend/Dojo/View/Helper/ComboBoxTest.php';
require_once 'Zend/Dojo/View/Helper/ContentPaneTest.php';
require_once 'Zend/Dojo/View/Helper/CurrencyTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/CustomDijitTest.php';
require_once 'Zend/Dojo/View/Helper/DateTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/FilteringSelectTest.php';
require_once 'Zend/Dojo/View/Helper/FormTest.php';
require_once 'Zend/Dojo/View/Helper/HorizontalSliderTest.php';
require_once 'Zend/Dojo/View/Helper/NumberSpinnerTest.php';
require_once 'Zend/Dojo/View/Helper/NumberTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/PasswordTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/RadioButtonTest.php';
require_once 'Zend/Dojo/View/Helper/SimpleTextareaTest.php';
require_once 'Zend/Dojo/View/Helper/SubmitButtonTest.php';
require_once 'Zend/Dojo/View/Helper/SplitContainerTest.php';
require_once 'Zend/Dojo/View/Helper/StackContainerTest.php';
require_once 'Zend/Dojo/View/Helper/TabContainerTest.php';
require_once 'Zend/Dojo/View/Helper/TextareaTest.php';
require_once 'Zend/Dojo/View/Helper/TextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/TimeTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/ValidationTextBoxTest.php';
require_once 'Zend/Dojo/View/Helper/VerticalSliderTest.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_View_Helper_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo - View_Helper');

        $suite->addTestSuite('Zend_Dojo_View_Helper_DojoTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_AccordionContainerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_AccordionPaneTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_BorderContainerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_ButtonTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_CheckBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_ComboBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_ContentPaneTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_CurrencyTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_CustomDijitTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_DateTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_FilteringSelectTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_FormTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_HorizontalSliderTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_NumberSpinnerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_NumberTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_PasswordTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_RadioButtonTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_SimpleTextareaTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_SubmitButtonTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_SplitContainerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_StackContainerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_TabContainerTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_TextareaTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_TextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_TimeTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_ValidationTextBoxTest');
        $suite->addTestSuite('Zend_Dojo_View_Helper_VerticalSliderTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_View_Helper_AllTests::main') {
    Zend_Dojo_View_Helper_AllTests::main();
}
