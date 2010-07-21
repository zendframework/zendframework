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
namespace ZendTest\Test\PHPUnit\Db;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class TestCaseTest extends \Zend\Test\PHPUnit\DatabaseTestCase
{
    /**
     * Contains a Database Connection
     *
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $_connectionMock = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if($this->_connectionMock == null) {
            $this->_connectionMock = $this->getMock(
                'Zend\Test\PHPUnit\Db\Connection', array(), array(new \Zend\Test\DbAdapter(), "schema")
            );
        }
        return $this->_connectionMock;
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
    }

    public function testDatabaseTesterIsInitialized()
    {
        $this->assertTrue($this->databaseTester instanceof \PHPUnit_Extensions_Database_ITester);
    }

    public function testDatabaseTesterNestsDefaultConnection()
    {
        $this->assertTrue($this->databaseTester->getConnection() instanceof \PHPUnit_Extensions_Database_DB_IDatabaseConnection);
    }

    public function testCheckZendDbConnectionConvenienceMethodReturnType()
    {
        $mock = $this->getMock('\Zend\Db\Adapter\Pdo\Sqlite', array('delete'), array(), 'Zend_DB_Adapter_Mock', false);
        $this->assertTrue($this->createZendDbConnection($mock, "test") instanceof \Zend\Test\PHPUnit\Db\Connection);
    }

    public function testCreateDbTableDataSetConvenienceMethodReturnType()
    {
        $tableMock = $this->getMock('\Zend\Db\Table\Table', array(), array(), "", false);
        $tableDataSet = $this->createDbTableDataSet(array($tableMock));
        $this->assertTrue($tableDataSet instanceof \Zend\Test\PHPUnit\Db\DataSet\DbTableDataSet);
    }

    public function testCreateDbTableConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend\Db\Table\Table', array(), array(), "", false);
        $table = $this->createDbTable($mock);
        $this->assertTrue($table instanceof \Zend\Test\PHPUnit\Db\DataSet\DbTable);
    }

    public function testCreateDbRowsetConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend\Db\Table\Rowset', array(), array(array()));
        $mock->expects($this->once())->method('toArray')->will($this->returnValue(array("foo" => 1, "bar" => 1)));

        $rowset = $this->createDbRowset($mock, "fooTable");

        $this->assertTrue($rowset instanceof \Zend\Test\PHPUnit\Db\DataSet\DbRowset);
    }

    public function testGetAdapterConvenienceMethod()
    {
        $this->_connectionMock->expects($this->once())
                              ->method('getConnection');
        $this->getAdapter();
    }
}
