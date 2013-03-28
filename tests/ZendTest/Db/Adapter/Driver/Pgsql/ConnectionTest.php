<?php
namespace ZendTest\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\Pgsql\Connection;

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
            $this->setExpectedException('Zend\Db\Adapter\Exception\RuntimeException');
            // Error supressing needed else pg_connect will fatal
            @$this->connection->getResource();
        } else {
            $this->markTestSkipped('pgsql extension not loaded');
        }
    }
}
