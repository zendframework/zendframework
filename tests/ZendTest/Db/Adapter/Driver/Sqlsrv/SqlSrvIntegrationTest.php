<?php
namespace ZendTest\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlsrvIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @group integration-sqlserver
     * @covers Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $sqlserver = new Sqlsrv(array());
        $this->assertNull($sqlserver->checkEnvironment());
    }

    public function testCreateStatement()
    {
        $driver = new Sqlsrv(array());

        $resource = sqlsrv_connect(
            $this->variables['hostname'], array(
                'UID' => $this->variables['username'],
                'PWD' => $this->variables['password']
            )
        );

        $driver->getConnection()->setResource($resource);

        $stmt = $driver->createStatement('SELECT 1');
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);
        $stmt = $driver->createStatement($resource);
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);
        $stmt = $driver->createStatement();
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);

        $this->setExpectedException('Zend\Db\Adapter\Exception\InvalidArgumentException', 'only accepts an SQL string or a Sqlsrv resource');
        $driver->createStatement(new \stdClass);
    }

}
