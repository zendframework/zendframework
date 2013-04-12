<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter\DbTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\ResultSet;

/**
 * @group Zend_Paginator
 */
class DbTableGatewayTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockStatement;

    /** @var DbTableGateway */
    protected $dbTableGateway;

    protected $mockTableGateway;

    public function setup()
    {
        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())
                   ->method('createStatement')
                   ->will($this->returnValue($mockStatement));
        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $mockPlatform->expects($this->any())
                     ->method('getName')
                     ->will($this->returnValue('platform'));
        $mockAdapter = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Adapter',
            array($mockDriver, $mockPlatform)
        );

        $tableName = 'foobar';
        $mockTableGateway = $this->getMockForAbstractClass(
            'Zend\Db\TableGateway\TableGateway',
            array($tableName, $mockAdapter)
        );

        $this->mockStatement = $mockStatement;

        $this->mockTableGateway = $mockTableGateway;
    }

    public function testGetItems()
    {
        $this->dbTableGateway = new DbTableGateway($this->mockTableGateway);

        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockStatement
             ->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($mockResult));

        $items = $this->dbTableGateway->getItems(2, 10);
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $items);
    }

    public function testCount()
    {
        $this->dbTableGateway = new DbTableGateway($this->mockTableGateway);

        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->any())
                   ->method('current')
                   ->will($this->returnValue(array('c' => 10)));

        $this->mockStatement->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($mockResult));

        $count = $this->dbTableGateway->count();
        $this->assertEquals(10, $count);
    }

    public function testGetItemsWithWhereAndOrder()
    {
        $where = "foo = bar";
        $order = "foo";
        $this->dbTableGateway = new DbTableGateway($this->mockTableGateway, $where, $order);

        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockStatement
             ->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($mockResult));

        $items = $this->dbTableGateway->getItems(2, 10);
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $items);
    }
}
