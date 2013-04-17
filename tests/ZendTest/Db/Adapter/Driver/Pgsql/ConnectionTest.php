<?php
namespace ZendTest\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\Pgsql\Connection;
use Zend\Db\Adapter\Exception as AdapterException;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->connection = new Connection();
    }

    /**
     * Test getResource method if it tries to connect to the database.
     *
     * @covers Zend\Db\Adapter\Driver\Pgsql\Connection::getResource
     */
    public function testResource()
    {
        if (extension_loaded('pgsql')) {
            try {
                $resource = $this->connection->getResource();
                // connected with empty string
                $this->assertTrue(is_resource($resource));
            } catch (AdapterException\RuntimeException $exc) {
                // If it throws an exception it has failed to connect
                $this->setExpectedException('Zend\Db\Adapter\Exception\RuntimeException');
                throw $exc;
            }
        } else {
            $this->markTestSkipped('pgsql extension not loaded');
        }
    }
}
