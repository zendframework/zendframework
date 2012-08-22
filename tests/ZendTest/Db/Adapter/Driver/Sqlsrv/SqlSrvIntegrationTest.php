<?php
namespace ZendTest\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv;

/**
 * @group integration
 * @group integration-sqlsrv
 */
class SqlsrvIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!extension_loaded('sqlsrv')) {
            $this->fail('The phpunit group integration-sqlsrv was enabled, but the extension is not loaded.');
        }
    }

    /**
     * @group integration-sqlserver
     * @covers Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $sqlserver = new Sqlsrv(array());
        $this->assertNull($sqlserver->checkEnvironment());
    }

}
