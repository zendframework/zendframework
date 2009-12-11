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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Test_PHPUnit_Db_AllTests::main');
}

require_once "DataSet/AllTests.php";
require_once "Operation/AllTests.php";
require_once "Metadata/GenericTest.php";
require_once "TestCaseTest.php";
require_once "ConnectionTest.php";
require_once "SimpleTesterTest.php";
require_once "Integration/SqLiteIntegrationTest.php";
require_once "Integration/MysqlIntegrationTest.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Test_PHPUnit_Db');

        $suite->addTestSuite('Zend_Test_PHPUnit_Db_TestCaseTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_ConnectionTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_SimpleTesterTest');
        $suite->addTest(Zend_Test_PHPUnit_Db_DataSet_AllTests::suite());
        $suite->addTest(Zend_Test_PHPUnit_Db_Operation_AllTests::suite());
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_Metadata_GenericTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_Integration_SqLiteIntegrationTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_Integration_MysqlIntegrationTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Test_PHPUnit_Db_AllTests::main') {
    Zend_Test_PHPUnit_Db_AllTests::main();
}
