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

require_once dirname(__FILE__)."/../../../../TestHelper.php";
require_once "PHPUnit/Extensions/Database/DataSet/CompositeDataSet.php";
require_once "Zend/Test/PHPUnit/DatabaseTestCase.php";
require_once "Zend/Db/Adapter/Abstract.php";
require_once "Zend/Db/Adapter/Pdo/Sqlite.php";
require_once "Zend/Db/Table.php";
require_once "Zend/Db/Table/Rowset.php";
require_once "Zend/Test/DbAdapter.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_TestCaseTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
                'Zend_Test_PHPUnit_Db_Connection', array(), array(new Zend_Test_DbAdapter(), "schema")
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
        return new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
    }

    public function testDatabaseTesterIsInitialized()
    {
        $this->assertTrue($this->databaseTester instanceof PHPUnit_Extensions_Database_ITester);
    }

    public function testDatabaseTesterNestsDefaultConnection()
    {
        $this->assertTrue($this->databaseTester->getConnection() instanceof PHPUnit_Extensions_Database_DB_IDatabaseConnection);
    }

    public function testCheckZendDbConnectionConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Adapter_Pdo_Sqlite', array('delete'), array(), "Zend_Db_Adapter_Mock", false);
        $this->assertTrue($this->createZendDbConnection($mock, "test") instanceof Zend_Test_PHPUnit_Db_Connection);
    }

    public function testCreateDbTableDataSetConvenienceMethodReturnType()
    {
        $tableMock = $this->getMock('Zend_Db_Table', array(), array(), "", false);
        $tableDataSet = $this->createDbTableDataSet(array($tableMock));
        $this->assertTrue($tableDataSet instanceof Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet);
    }

    public function testCreateDbTableConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table', array(), array(), "", false);
        $table = $this->createDbTable($mock);
        $this->assertTrue($table instanceof Zend_Test_PHPUnit_Db_DataSet_DbTable);
    }

    public function testCreateDbRowsetConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table_Rowset', array(), array(array()));
        $mock->expects($this->once())->method('toArray')->will($this->returnValue(array("foo" => 1, "bar" => 1)));
        
        $rowset = $this->createDbRowset($mock, "fooTable");

        $this->assertTrue($rowset instanceof Zend_Test_PHPUnit_Db_DataSet_DbRowset);
    }

    public function testGetAdapterConvenienceMethod()
    {
        $this->_connectionMock->expects($this->once())
                              ->method('getConnection');
        $this->getAdapter();
    }
}
