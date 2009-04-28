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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../.././../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_Action_Helper_AllTests::main');
}

require_once 'Zend/Controller/Action/Helper/ActionStackTest.php';
require_once 'Zend/Controller/Action/Helper/AjaxContextTest.php';
require_once 'Zend/Controller/Action/Helper/AutoCompleteTest.php';
require_once 'Zend/Controller/Action/Helper/ContextSwitchTest.php';
require_once 'Zend/Controller/Action/Helper/FlashMessengerTest.php';
require_once 'Zend/Controller/Action/Helper/JsonTest.php';
require_once 'Zend/Controller/Action/Helper/RedirectorTest.php';
require_once 'Zend/Controller/Action/Helper/UrlTest.php';
require_once 'Zend/Controller/Action/Helper/ViewRendererTest.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Controller');

        $suite->addTestSuite('Zend_Controller_Action_Helper_ActionStackTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_AutoCompleteTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_ContextSwitchTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_AjaxContextTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_FlashMessengerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_JsonTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_RedirectorTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_UrlTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_ViewRendererTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Controller_Action_Helper_AllTests::main') {
    Zend_Controller_Action_Helper_AllTests::main();
}
