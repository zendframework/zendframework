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
 * @see Zend_Test_PHPUnit_Db_DataSet_DataSetTestCase
 */

/**
 * @see Zend_Test_PHPUnit_Db_DataSet_QueryTable
 */

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_DataSet_QueryDataSetTest extends Zend_Test_PHPUnit_Db_DataSet_DataSetTestCase
{
    public function testCreateQueryDataSetWithoutZendDbAdapterThrowsException()
    {
        $connectionMock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        $this->setExpectedException('Zend_Test_PHPUnit_Db_Exception');
        $queryDataSet = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($connectionMock);
    }

    public function testCreateQueryDataSetWithZendDbAdapter()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryDataSet = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($this->connectionMock);
    }

    public function testAddTableWithoutQueryParameterCreatesSelectWildcardAll()
    {
        $fixtureTableName = "foo";

        $adapterMock = $this->getMock('Zend_Test_DbAdapter');
        $selectMock = $this->getMock('Zend_Db_Select', array(), array($adapterMock));

        $adapterMock->expects($this->once())
                    ->method('select')
                    ->will($this->returnValue($selectMock));
        $this->decorateConnectionGetConnectionWith($adapterMock);

        $selectMock->expects($this->once())
                   ->method('from')
                   ->with($fixtureTableName, Zend_Db_Select::SQL_WILDCARD);
        $selectMock->expects($this->once())
                   ->method('__toString')
                   ->will($this->returnValue('SELECT * FOM foo'));

        $queryDataSet = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($this->connectionMock);
        $queryDataSet->addTable('foo');
    }
}
