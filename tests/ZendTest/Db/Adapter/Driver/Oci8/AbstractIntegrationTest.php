<?php

namespace ZendTest\Db\Adapter\Driver\Oci8;

abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $variables = array(
        'hostname' => 'ZEND_DB_ADAPTER_DRIVER_OCI8_HOSTNAME',
        'username' => 'ZEND_DB_ADAPTER_DRIVER_OCI8_USERNAME',
        'password' => 'ZEND_DB_ADAPTER_DRIVER_OCI8_PASSWORD',
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

        if (!extension_loaded('oci8')) {
            $this->fail('The phpunit group integration-oci8 was enabled, but the extension is not loaded.');
        }
    }
}
