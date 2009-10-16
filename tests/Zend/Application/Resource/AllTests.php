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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_AllTests::main');
}

require_once 'Zend/Application/Resource/ResourceAbstractTest.php';
require_once 'Zend/Application/Resource/DbTest.php';
require_once 'Zend/Application/Resource/DojoTest.php';
require_once 'Zend/Application/Resource/FrontcontrollerTest.php';
require_once 'Zend/Application/Resource/LayoutTest.php';
require_once 'Zend/Application/Resource/LocaleTest.php';
require_once 'Zend/Application/Resource/ModulesTest.php';
require_once 'Zend/Application/Resource/NavigationTest.php';
require_once 'Zend/Application/Resource/SessionTest.php';
require_once 'Zend/Application/Resource/ViewTest.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 * @group      Zend_Application_Resource
 */
class Zend_Application_Resource_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Application_Resource');

        $suite->addTestSuite('Zend_Application_Resource_ResourceAbstractTest');
        $suite->addTestSuite('Zend_Application_Resource_DbTest');
        $suite->addTestSuite('Zend_Application_Resource_DojoTest');        
        $suite->addTestSuite('Zend_Application_Resource_FrontcontrollerTest');
        $suite->addTestSuite('Zend_Application_Resource_LayoutTest');
        $suite->addTestSuite('Zend_Application_Resource_LocaleTest');
        $suite->addTestSuite('Zend_Application_Resource_ModulesTest');
        $suite->addTestSuite('Zend_Application_Resource_NavigationTest');
        $suite->addTestSuite('Zend_Application_Resource_SessionTest');
        $suite->addTestSuite('Zend_Application_Resource_ViewTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_AllTests::main') {
    Zend_Application_Resource_AllTests::main();
}
