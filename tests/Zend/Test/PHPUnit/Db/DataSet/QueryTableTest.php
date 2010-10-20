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

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db\DataSet;
use Zend\Test\PHPUnit\Db\DataSet;
use Zend\Test;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class QueryTableTest extends DataSetTestCase
{
    public function testCreateQueryTableWithoutZendDbConnectionThrowsException()
    {
        $connectionMock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->setExpectedException('Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException');
        $queryTable = new DataSet\QueryTable("foo", "SELECT * FROM foo", $connectionMock);
    }

    public function testCreateQueryTableWithZendDbConnection()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryTable = new DataSet\QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
    }

    public function testLoadDataExecutesQueryOnZendAdapter()
    {
        $statementMock = new Test\DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = new Test\DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new DataSet\QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $data = $queryTable->getRow(0);

        $this->assertEquals(
            array("foo" => "bar"), $data
        );
    }

    public function testGetRowCountLoadsData()
    {
        $statementMock = new Test\DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = new Test\DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new DataSet\QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $count = $queryTable->getRowCount();

        $this->assertEquals(1, $count);
    }

    public function testDataIsLoadedOnlyOnce()
    {
        $fixtureSql = "SELECT * FROM foo";

        $statementMock = new Test\DbStatement();
        $statementMock->append(array('foo' => 'bar'));
        $adapterMock = $this->getMock('Zend\Test\DbAdapter');
        $adapterMock->expects($this->once())
                    ->method('query')
                    ->with($fixtureSql)
                    ->will($this->returnValue($statementMock));

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new DataSet\QueryTable("foo", $fixtureSql, $this->connectionMock);
        $this->assertEquals(1, $queryTable->getRowCount());
        $this->assertEquals(1, $queryTable->getRowCount());
        $row = $queryTable->getRow(0);
        $this->assertEquals(array('foo' => 'bar'), $row);
    }

    public function testQueryTableWithoutRows()
    {
        $statementMock = new Test\DbStatement();
        $adapterMock = new Test\DbAdapter();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);
        $queryTable = new DataSet\QueryTable("foo", null, $this->connectionMock);

        $metadata = $queryTable->getTableMetaData();
        $this->assertType('PHPUnit_Extensions_Database_DataSet_ITableMetaData', $metadata);
        $this->assertEquals(array(), $metadata->getColumns());
        $this->assertEquals(array(), $metadata->getPrimaryKeys());
        $this->assertEquals("foo", $metadata->getTableName());
    }
}
