<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter\DbSelect;

/**
 * @group      Zend_Paginator
 */
class DbSelectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Db\Sql\Select */
    protected $mockSelect;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Db\Sql\Select */
    protected $mockSelectCount;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Db\Adapter\Driver\StatementInterface */
    protected $mockStatement;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Db\Adapter\Driver\ResultInterface */
    protected $mockResult;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Db\Sql\Sql */
    protected $mockSql;

    /** @var DbSelect */
    protected $dbSelect;

    public function setUp()
    {
        $this->mockResult    = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');

        $this->mockStatement->expects($this->any())->method('execute')->will($this->returnValue($this->mockResult));

        $mockDriver   = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');

        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($this->mockStatement));
        $mockPlatform->expects($this->any())->method('getName')->will($this->returnValue('platform'));

        $this->mockSql = $this->getMock(
            'Zend\Db\Sql\Sql',
            array('prepareStatementForSqlObject', 'execute'),
            array($this->getMockForAbstractClass('Zend\Db\Adapter\Adapter', array($mockDriver, $mockPlatform)))
        );

        $this
            ->mockSql
            ->expects($this->any())
            ->method('prepareStatementForSqlObject')
            ->with($this->isInstanceOf('Zend\Db\Sql\Select'))
            ->will($this->returnValue($this->mockStatement));

        $this->mockSelect      = $this->getMock('Zend\Db\Sql\Select');
        $this->mockSelectCount = $this->getMock('Zend\Db\Sql\Select');
        $this->dbSelect        = new DbSelect($this->mockSelect, $this->mockSql);
    }

    public function testGetItems()
    {
        $this->mockSelect->expects($this->once())->method('limit')->with($this->equalTo(10));
        $this->mockSelect->expects($this->once())->method('offset')->with($this->equalTo(2));
        $items = $this->dbSelect->getItems(2, 10);
        $this->assertEquals(array(), $items);
    }

    public function testCount()
    {
        $this->mockResult->expects($this->once())->method('current')->will($this->returnValue(array(DbSelect::ROW_COUNT_COLUMN_NAME => 5)));

        $this->mockSelect->expects($this->exactly(3))->method('reset'); // called for columns, limit, offset, order

        $count = $this->dbSelect->count();
        $this->assertEquals(5, $count);
    }

    public function testCustomCount()
    {
        $this->dbSelect = new DbSelect($this->mockSelect, $this->mockSql, null, $this->mockSelectCount);
        $this->mockResult->expects($this->once())->method('current')->will($this->returnValue(array(DbSelect::ROW_COUNT_COLUMN_NAME => 7)));

        $count = $this->dbSelect->count();
        $this->assertEquals(7, $count);
    }

    /**
     * @group 6817
     * @group 6812
     */
    public function testReturnValueIsArray()
    {
        $this->assertInternalType('array', $this->dbSelect->getItems(0, 10));
    }
}
