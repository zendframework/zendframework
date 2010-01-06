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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_AllTests::main');
}

require_once 'Zend/Application/ApplicationTest.php';
require_once 'Zend/Application/Bootstrap/BootstrapAbstractTest.php';
require_once 'Zend/Application/Module/AutoloaderTest.php';
require_once 'Zend/Application/Module/BootstrapTest.php';
require_once 'Zend/Application/Resource/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Application');

        $suite->addTestSuite('Zend_Application_ApplicationTest');
        $suite->addTestSuite('Zend_Application_Bootstrap_BootstrapAbstractTest');
        $suite->addTestSuite('Zend_Application_Module_AutoloaderTest');
        $suite->addTestSuite('Zend_Application_Module_BootstrapTest');
        $suite->addTest(Zend_Application_Resource_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_AllTests::main') {
    Zend_Application_AllTests::main();
}
