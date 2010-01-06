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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__)."/../../../../../TestHelper.php";

/**
 * @see Zend_Test_PHPUnit_Db_DataSet_DataSetTestCase
 */
require_once "Zend/Test/PHPUnit/Db/DataSet/DataSetTestCase.php";

require_once "Zend/Db/Statement/Interface.php";

require_once "Zend/Test/DbAdapter.php";

require_once "Zend/Test/DbStatement.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_DataSet_QueryTableTest extends Zend_Test_PHPUnit_Db_DataSet_DataSetTestCase
{
    public function testCreateQueryTableWithoutZendDbConnectionThrowsException()
    {
        $connectionMock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->setExpectedException('Zend_Test_PHPUnit_Db_Exception');
        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", "SELECT * FROM foo", $connectionMock);
    }

    public function testCreateQueryTableWithZendDbConnection()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
    }

    public function testLoadDataExecutesQueryOnZendAdapter()
    {
        $statementMock = new Zend_Test_DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = new Zend_Test_DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $data = $queryTable->getRow(0);

        $this->assertEquals(
            array("foo" => "bar"), $data
        );
    }

    public function testGetRowCountLoadsData()
    {
        $statementMock = new Zend_Test_DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = new Zend_Test_DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $count = $queryTable->getRowCount();

        $this->assertEquals(1, $count);
    }

    public function testDataIsLoadedOnlyOnce()
    {
        $fixtureSql = "SELECT * FROM foo";

        $statementMock = new Zend_Test_DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = $this->getMock('Zend_Test_DbAdapter');
        $adapterMock->expects($this->once())
                    ->method('query')
                    ->with($fixtureSql)
                    ->will($this->returnValue($statementMock));

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", $fixtureSql, $this->connectionMock);
        $this->assertEquals(1, $queryTable->getRowCount());
        $this->assertEquals(1, $queryTable->getRowCount());
        $row = $queryTable->getRow(0);
        $this->assertEquals(array('foo' => 'bar'), $row);
    }

    public function testQueryTableWithoutRows()
    {
        $statementMock = new Zend_Test_DbStatement();
        $adapterMock = new Zend_Test_DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);
        $queryTable = new Zend_Test_PHPUnit_Db_DataSet_QueryTable("foo", null, $this->connectionMock);

        $metadata = $queryTable->getTableMetaData();
        $this->assertType('PHPUnit_Extensions_Database_DataSet_ITableMetaData', $metadata);
        $this->assertEquals(array(), $metadata->getColumns());
        $this->assertEquals(array(), $metadata->getPrimaryKeys());
        $this->assertEquals("foo", $metadata->getTableName());
    }
}
