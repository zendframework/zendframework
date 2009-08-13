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

require_once dirname(__FILE__)."/../../../../../TestHelper.php";

require_once "DbRowsetTest.php";
require_once "QueryDataSetTest.php";
require_once "QueryTableTest.php";
require_once "DbTableTest.php";
require_once "DbTableDataSetTest.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_DataSet_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database DataSets');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_DataSet_DbRowsetTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_DataSet_QueryDataSetTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_DataSet_QueryTableTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_DataSet_DbTableTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Db_DataSet_DbTableDataSetTest');

        return $suite;
    }
}
