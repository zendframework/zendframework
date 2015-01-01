<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Pgsql;

use ReflectionMethod;
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
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        try {
            $resource = $this->connection->getResource();
            // connected with empty string
            $this->assertTrue(is_resource($resource));
        } catch (AdapterException\RuntimeException $exc) {
            // If it throws an exception it has failed to connect
            $this->setExpectedException('Zend\Db\Adapter\Exception\RuntimeException');
            throw $exc;
        }
    }

    /**
     * @group 6760
     * @group 6787
     */
    public function testGetConnectionStringEncodeSpecialSymbol()
    {
        $connectionParameters = array(
            'driver'    => 'pgsql',
            'host' => 'localhost',
            'post' => '5432',
            'dbname' => 'test',
            'username'  => 'test',
            'password'  => 'test123!',
        );

        $this->connection->setConnectionParameters($connectionParameters);

        $getConnectionString = new ReflectionMethod(
            'Zend\Db\Adapter\Driver\Pgsql\Connection',
            'getConnectionString'
        );

        $getConnectionString->setAccessible(true);

        $this->assertEquals(
            'host=localhost user=test password=test123! dbname=test',
            $getConnectionString->invoke($this->connection)
        );
    }
}
