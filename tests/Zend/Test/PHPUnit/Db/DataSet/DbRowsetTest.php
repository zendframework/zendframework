<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\DataSet;
use Zend\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class DbRowsetTest extends \PHPUnit_Framework_TestCase
{
    protected function getRowSet()
    {
        $config = array(
            'rowClass' => 'stdClass',
            'data'     => array(array('foo' => 'bar'), array('foo' => 'baz')),
        );
        $rowset = new \Zend\Db\Table\Rowset($config);
        return $rowset;
    }

    public function testRowsetCountInITableRepresentation()
    {
        $rowsetTable = new DataSet\DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(2, $rowsetTable->getRowCount());
    }

    public function testRowsetGetSpecificValue()
    {
        $rowsetTable = new DataSet\DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals("bar", $rowsetTable->getValue(0, "foo"));
    }

    public function testRowsetGetSpecificRow()
    {
        $rowsetTable = new DataSet\DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(array("foo" => "baz"), $rowsetTable->getRow(1));
    }

    public function testRowset_ConstructWithDisconnectedRowset_NoTableName_ThrowsException()
    {
        $this->setExpectedException("Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException");

        $rowset = $this->getMock('Zend\Db\Table\AbstractRowset', array(), array(), '', false);
        $rowset->expects($this->once())
               ->method('getTable')
               ->will($this->returnValue(null));

        $rowsetTable = new DataSet\DbRowset($rowset);
    }

    public function testRowset_WithNoRows_GetColumnsFromTable()
    {
        $columns = array("foo", "bar");

        $tableMock = $this->getMock('Zend\Db\Table\AbstractTable', array(), array(), '', false);
        $tableMock->expects($this->once())
                  ->method('info')
                  ->with($this->equalTo('cols'))
                  ->will($this->returnValue($columns));

        $rowset = $this->getMock('Zend\Db\Table\AbstractRowset', array(), array(), '', false);
        $rowset->expects($this->exactly(2))
               ->method('getTable')
               ->will($this->returnValue($tableMock));
        $rowset->expects($this->once())
               ->method('toArray')
               ->will($this->returnValue( array() ));

        $rowsetTable = new DataSet\DbRowset($rowset, "tableName");
    }
}
