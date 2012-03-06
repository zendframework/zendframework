<?php
namespace ZendTest\Db\Adapter;

use Zend\Db\Adapter\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDriver = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockPlatform = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockConnection = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStatement = null;

    /**
     * @var Adapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $this->mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $this->mockDriver->expects($this->any())->method('checkEnvironment')->will($this->returnValue(true));
        $this->mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($this->mockConnection));
        $this->mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $this->mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $this->mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($this->mockStatement));

        $this->adapter = new Adapter($this->mockDriver, $this->mockPlatform);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::__construct
     */
    public function testAdapterWillConstruct()
    {
        $adapter = new Adapter($this->mockDriver, $this->mockPlatform);
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $adapter);
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::createDriverFromParameters
     */
    public function testAdapterWillConstructDriverFromArray()
    {
        if (extension_loaded('mysqli')) {
            $adapter = new Adapter(array('driver' => 'mysqli'), $this->mockPlatform);
            $this->assertInstanceOf('Zend\Db\Adapter\Driver\Mysqli\Mysqli', $adapter->driver);
            unset($adapter);
        }

        if (extension_loaded('sqlsrv')) {
            $adapter = new Adapter(array('driver' => 'sqlsrv'), $this->mockPlatform);
            $this->assertInstanceOf('Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv', $adapter->driver);
            unset($adapter);
        }

        if (extension_loaded('pdo')) {
            $adapter = new Adapter(array('driver' => 'pdo_sqlite'), $this->mockPlatform);
            $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Pdo', $adapter->driver);
            unset($adapter);
        }
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::createPlatformFromDriver
     */
    public function testAdapterWillConstructPlatformFromDriver()
    {
        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Mysql'));
        $adapter = new Adapter($driver);
        $this->assertInstanceOf('Zend\Db\Adapter\Platform\Mysql', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('SqlServer'));
        $adapter = new Adapter($driver);
        $this->assertInstanceOf('Zend\Db\Adapter\Platform\SqlServer', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Sqlite'));
        $adapter = new Adapter($driver);
        $this->assertInstanceOf('Zend\Db\Adapter\Platform\Sqlite', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Foo'));
        $adapter = new Adapter($driver);
        $this->assertInstanceOf('Zend\Db\Adapter\Platform\Sql92', $adapter->platform);
        unset($adapter, $driver);

    }


    /**
     * @covers Zend\Db\Adapter\Adapter::getDriver
     */
    public function testGetDriver()
    {
        $this->assertSame($this->mockDriver, $this->adapter->getDriver());
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::setQueryMode
     */
    public function testSetQueryMode()
    {
        $this->adapter->setQueryMode(Adapter::QUERY_MODE_EXECUTE);
        $this->adapter->setQueryMode(Adapter::QUERY_MODE_PREPARE);

        $this->setExpectedException('InvalidArgumentException', 'Query Mode must be one of');
        $this->adapter->setQueryMode('foo');
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::getPlatform
     */
    public function testGetPlatform()
    {
        $this->assertSame($this->mockPlatform, $this->adapter->getPlatform());
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::getDefaultSchema
     */
    public function testGetDefaultSchema()
    {
        $this->mockConnection->expects($this->any())->method('getDefaultSchema')->will($this->returnValue('FooSchema'));
        $this->assertEquals('FooSchema', $this->adapter->getDefaultSchema());
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::query
     */
    public function testQueryWhenPreparedProducesStatement()
    {
        $s = $this->adapter->query('SELECT foo');
        $this->assertSame($this->mockStatement, $s);
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::query
     */
    public function testQueryWhenExecutedProducesAResult()
    {
        $sql = 'SELECT foo';
        $result = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockConnection->expects($this->any())->method('execute')->with($sql)->will($this->returnValue($result));

        $r = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $this->assertSame($result, $r);
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::query
     */
    public function testQueryWhenExecutedProducesAResultSetObjectWhenResultIsQuery()
    {
        $sql = 'SELECT foo';

        $result = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockConnection->expects($this->any())->method('execute')->with($sql)->will($this->returnValue($result));
        $result->expects($this->any())->method('isQueryResult')->will($this->returnValue(true));

        $r = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $r);
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::createStatement
     */
    public function testCreateStatement()
    {
        $this->assertSame($this->mockStatement, $this->adapter->createStatement());
    }

    /**
     * @covers Zend\Db\Adapter\Adapter::__get
     */
    public function test__get()
    {
        $this->assertSame($this->mockDriver, $this->adapter->driver);
        $this->assertSame($this->mockDriver, $this->adapter->DrivER);
        $this->assertSame($this->mockPlatform, $this->adapter->PlatForm);
        $this->assertSame($this->mockPlatform, $this->adapter->platform);

        $this->setExpectedException('InvalidArgumentException', 'Invalid magic');
        $this->adapter->foo;
    }
}
