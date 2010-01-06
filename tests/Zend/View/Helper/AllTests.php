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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_AllTests::main');
}

require_once 'Zend/View/Helper/ActionTest.php';
require_once 'Zend/View/Helper/BaseUrlTest.php';
require_once 'Zend/View/Helper/CycleTest.php';
require_once 'Zend/View/Helper/DeclareVarsTest.php';
require_once 'Zend/View/Helper/DoctypeTest.php';
require_once 'Zend/View/Helper/FieldsetTest.php';
require_once 'Zend/View/Helper/FormButtonTest.php';
require_once 'Zend/View/Helper/FormCheckboxTest.php';
require_once 'Zend/View/Helper/FormErrorsTest.php';
require_once 'Zend/View/Helper/FormFileTest.php';
require_once 'Zend/View/Helper/FormImageTest.php';
require_once 'Zend/View/Helper/FormLabelTest.php';
require_once 'Zend/View/Helper/FormMultiCheckboxTest.php';
require_once 'Zend/View/Helper/FormPasswordTest.php';
require_once 'Zend/View/Helper/FormRadioTest.php';
require_once 'Zend/View/Helper/FormResetTest.php';
require_once 'Zend/View/Helper/FormSelectTest.php';
require_once 'Zend/View/Helper/FormSubmitTest.php';
require_once 'Zend/View/Helper/FormTest.php';
require_once 'Zend/View/Helper/FormTextTest.php';
require_once 'Zend/View/Helper/FormTextareaTest.php';
require_once 'Zend/View/Helper/HeadLinkTest.php';
require_once 'Zend/View/Helper/HeadMetaTest.php';
require_once 'Zend/View/Helper/HeadScriptTest.php';
require_once 'Zend/View/Helper/HeadStyleTest.php';
require_once 'Zend/View/Helper/HeadTitleTest.php';
require_once 'Zend/View/Helper/HtmlFlashTest.php';
require_once 'Zend/View/Helper/HtmlListTest.php';
require_once 'Zend/View/Helper/HtmlObjectTest.php';
require_once 'Zend/View/Helper/HtmlPageTest.php';
require_once 'Zend/View/Helper/HtmlQuicktimeTest.php';
require_once 'Zend/View/Helper/InlineScriptTest.php';
require_once 'Zend/View/Helper/JsonTest.php';
require_once 'Zend/View/Helper/LayoutTest.php';
require_once 'Zend/View/Helper/Navigation/AllTests.php';
require_once 'Zend/View/Helper/PaginationControlTest.php';
require_once 'Zend/View/Helper/PartialTest.php';
require_once 'Zend/View/Helper/PartialLoopTest.php';
require_once 'Zend/View/Helper/PlaceholderTest.php';
require_once 'Zend/View/Helper/Placeholder/ContainerTest.php';
require_once 'Zend/View/Helper/Placeholder/RegistryTest.php';
require_once 'Zend/View/Helper/Placeholder/StandaloneContainerTest.php';
require_once 'Zend/View/Helper/ServerUrlTest.php';
require_once 'Zend/View/Helper/TranslateTest.php';
require_once 'Zend/View/Helper/UrlTest.php';


/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_View_Helper');

        $suite->addTestSuite('Zend_View_Helper_ActionTest');
        $suite->addTestSuite('Zend_View_Helper_BaseUrlTest');
        $suite->addTestSuite('Zend_View_Helper_CycleTest');
        $suite->addTestSuite('Zend_View_Helper_DeclareVarsTest');
        $suite->addTestSuite('Zend_View_Helper_DoctypeTest');
        $suite->addTestSuite('Zend_View_Helper_FieldsetTest');
        $suite->addTestSuite('Zend_View_Helper_FormButtonTest');
        $suite->addTestSuite('Zend_View_Helper_FormCheckboxTest');
        $suite->addTestSuite('Zend_View_Helper_FormErrorsTest');
        $suite->addTestSuite('Zend_View_Helper_FormFileTest');
        $suite->addTestSuite('Zend_View_Helper_FormImageTest');
        $suite->addTestSuite('Zend_View_Helper_FormLabelTest');
        $suite->addTestSuite('Zend_View_Helper_FormMultiCheckboxTest');
        $suite->addTestSuite('Zend_View_Helper_FormPasswordTest');
        $suite->addTestSuite('Zend_View_Helper_FormRadioTest');
        $suite->addTestSuite('Zend_View_Helper_FormResetTest');
        $suite->addTestSuite('Zend_View_Helper_FormSelectTest');
        $suite->addTestSuite('Zend_View_Helper_FormSubmitTest');
        $suite->addTestSuite('Zend_View_Helper_FormTest');
        $suite->addTestSuite('Zend_View_Helper_FormTextTest');
        $suite->addTestSuite('Zend_View_Helper_FormTextareaTest');
        $suite->addTestSuite('Zend_View_Helper_HeadLinkTest');
        $suite->addTestSuite('Zend_View_Helper_HeadMetaTest');
        $suite->addTestSuite('Zend_View_Helper_HeadScriptTest');
        $suite->addTestSuite('Zend_View_Helper_HeadStyleTest');
        $suite->addTestSuite('Zend_View_Helper_HeadTitleTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlFlashTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlListTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlObjectTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlPageTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlQuicktimeTest');
        $suite->addTestSuite('Zend_View_Helper_InlineScriptTest');
        $suite->addTestSuite('Zend_View_Helper_JsonTest');
        $suite->addTestSuite('Zend_View_Helper_LayoutTest');
        $suite->addTest(Zend_View_Helper_Navigation_AllTests::suite());
        $suite->addTestSuite('Zend_View_Helper_PaginationControlTest');
        $suite->addTestSuite('Zend_View_Helper_PartialTest');
        $suite->addTestSuite('Zend_View_Helper_PartialLoopTest');
        $suite->addTestSuite('Zend_View_Helper_PlaceholderTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_ContainerTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_RegistryTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_StandaloneContainerTest');
        $suite->addTestSuite('Zend_View_Helper_ServerUrlTest');
        $suite->addTestSuite('Zend_View_Helper_TranslateTest');
        $suite->addTestSuite('Zend_View_Helper_UrlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_AllTests::main') {
    Zend_View_Helper_AllTests::main();
}
