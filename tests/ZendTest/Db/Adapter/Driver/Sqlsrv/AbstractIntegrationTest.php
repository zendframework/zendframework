<?php

namespace ZendTest\Db\Adapter\Driver\Sqlsrv;

abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $variables = array(
        'hostname' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME',
        'username' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_USERNAME',
        'password' => 'ZEND_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        foreach ($this->variables as $name => $value) {
            if (!isset($GLOBALS[$value])) {
                $this->fail('Missing required variable ' . $value . ' from phpunit.xml for this integration test');
            }
            $this->variables[$name] = $GLOBALS[$value];
        }

        if (!extension_loaded('sqlsrv')) {
            $this->fail('The phpunit group integration-sqlsrv was enabled, but the extension is not loaded.');
        }
    }
}
