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

require_once "Zend/Test/PHPUnit/Db/DataSet/DbRowset.php";
require_once "Zend/Db/Table/Rowset.php";
require_once "PHPUnit/Extensions/Database/DataSet/DefaultTableMetaData.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_DataSet_DbRowsetTest extends PHPUnit_Framework_TestCase
{
    protected function getRowSet()
    {
        $config = array(
            'rowClass' => 'stdClass',
            'data'     => array(array('foo' => 'bar'), array('foo' => 'baz')),
        );
        $rowset = new Zend_Db_Table_Rowset($config);
        return $rowset;
    }

    public function testRowsetCountInITableRepresentation()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(2, $rowsetTable->getRowCount());
    }

    public function testRowsetGetSpecificValue()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals("bar", $rowsetTable->getValue(0, "foo"));
    }

    public function testRowsetGetSpecificRow()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(array("foo" => "baz"), $rowsetTable->getRow(1));
    }

    public function testRowset_ConstructWithDisconnectedRowset_NoTableName_ThrowsException()
    {
        $this->setExpectedException("Zend_Test_PHPUnit_Db_Exception");

        $rowset = $this->getMock('Zend_Db_Table_Rowset_Abstract', array(), array(), '', false);
        $rowset->expects($this->once())
               ->method('getTable')
               ->will($this->returnValue(null));

        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
    }

    public function testRowset_WithNoRows_GetColumnsFromTable()
    {
        $columns = array("foo", "bar");

        $tableMock = $this->getMock('Zend_Db_Table_Abstract', array(), array(), '', false);
        $tableMock->expects($this->once())
                  ->method('info')
                  ->with($this->equalTo('cols'))
                  ->will($this->returnValue($columns));

        $rowset = $this->getMock('Zend_Db_Table_Rowset_Abstract', array(), array(), '', false);
        $rowset->expects($this->exactly(2))
               ->method('getTable')
               ->will($this->returnValue($tableMock));
        $rowset->expects($this->once())
               ->method('toArray')
               ->will($this->returnValue( array() ));

        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset, "tableName");
    }
}
