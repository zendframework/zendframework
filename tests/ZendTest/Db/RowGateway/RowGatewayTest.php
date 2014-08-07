<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\RowGateway;

use Zend\Db\RowGateway\RowGateway;

class RowGatewayTest extends \PHPUnit_Framework_TestCase
{

    protected $mockAdapter = null;

    protected $rowGateway = null;

    public function setup()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(1));
        $this->mockResult = $mockResult;
        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));
    }

    public function testEmptyPrimaryKey()
    {
        try {
            $this->rowGateway = new \Zend\Db\RowGateway\RowGateway('', 'foo', $this->mockAdapter);
            
            $this->fail('Excepcted null primary key exception not thrown');
        } catch (\Zend\Db\RowGateway\Exception\RuntimeException $e) {
            $this->assertEquals($e->getMessage(), 'This row object does not have a primary key column set.');
        }
    }


    protected function setRowGatewayState(array $properties)
    {
        $refRowGateway = new \ReflectionObject($this->rowGateway);
        foreach ($properties as $rgPropertyName => $rgPropertyValue) {
            $refRowGatewayProp = $refRowGateway->getProperty($rgPropertyName);
            $refRowGatewayProp->setAccessible(true);
            $refRowGatewayProp->setValue($this->rowGateway, $rgPropertyValue);
        }
    }
}