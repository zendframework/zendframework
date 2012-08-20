<?php
namespace ZendTest\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv;

/**
 * @group integration
 * @group integration-sqlsrv
 */
class SqlsrvIntegrationTest extends \PHPUnit_Framework_TestCase
{
//    protected $variables = array(
//        'hostname' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME',
//        'username' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_USERNAME',
//        'password' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD',
//        'database' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_DATABASE',
//    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
//        foreach ($this->variables as $name => $value) {
//            if (!isset($GLOBALS[$value])) {
//                $this->markTestSkipped('Missing required variable ' . $value . ' from phpunit.xml for this integration test');
//            }
//        }

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
