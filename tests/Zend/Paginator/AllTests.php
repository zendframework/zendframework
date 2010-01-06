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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Paginator_AllTests::main');
}

require_once 'Zend/PaginatorTest.php';

require_once 'Zend/Paginator/Adapter/ArrayTest.php';
require_once 'Zend/Paginator/Adapter/DbSelectTest.php';
require_once 'Zend/Paginator/Adapter/DbSelect/OracleTest.php';
require_once 'Zend/Paginator/Adapter/DbTableSelectTest.php';
require_once 'Zend/Paginator/Adapter/DbTableSelect/OracleTest.php';
require_once 'Zend/Paginator/Adapter/IteratorTest.php';
require_once 'Zend/Paginator/Adapter/NullTest.php';

require_once 'Zend/Paginator/ScrollingStyle/AllTest.php';
require_once 'Zend/Paginator/ScrollingStyle/ElasticTest.php';
require_once 'Zend/Paginator/ScrollingStyle/JumpingTest.php';
require_once 'Zend/Paginator/ScrollingStyle/SlidingTest.php';

require_once 'Zend/View/Helper/PaginationControlTest.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class Zend_Paginator_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Paginator');
        $suite->addTestSuite('Zend_PaginatorTest');

        $suite->addTestSuite('Zend_Paginator_Adapter_ArrayTest');
        $suite->addTestSuite('Zend_Paginator_Adapter_DbSelectTest');
        $suite->addTestSuite('Zend_Paginator_Adapter_DbTableSelectTest');
        $suite->addTestSuite('Zend_Paginator_Adapter_IteratorTest');
        $suite->addTestSuite('Zend_Paginator_Adapter_NullTest');

        if (TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED) {
            $suite->addTestSuite('Zend_Paginator_Adapter_DbSelect_OracleTest');
            $suite->addTestSuite('Zend_Paginator_Adapter_DbTableSelect_OracleTest');
        }

        $suite->addTestSuite('Zend_Paginator_ScrollingStyle_AllTest');
        $suite->addTestSuite('Zend_Paginator_ScrollingStyle_ElasticTest');
        $suite->addTestSuite('Zend_Paginator_ScrollingStyle_JumpingTest');
        $suite->addTestSuite('Zend_Paginator_ScrollingStyle_SlidingTest');

        $suite->addTestSuite('Zend_View_Helper_PaginationControlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Paginator_AllTests::main') {
    Zend_Paginator_AllTests::main();
}
