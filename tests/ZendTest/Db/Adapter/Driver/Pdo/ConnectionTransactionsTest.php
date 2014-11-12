<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\Pdo\Connection;
use ZendTest\Db\Adapter\Driver\TestAsset\PdoMock;

/**
 * Tests for {@see \Zend\Db\Adapter\Driver\Pdo\Connection} transaction support
 *
 * @covers \Zend\Db\Adapter\Driver\Pdo\Connection
 * @covers \Zend\Db\Adapter\Driver\AbstractConnection
 */
class ConnectionTransactionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->connection = new Connection();

        $this->connection->setResource(new PdoMock());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     */
    public function testBeginTransactionReturnsInstanceOfConnection()
    {
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Connection', $this->connection->beginTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testBeginTransactionSetsInTransactionAtTrue()
    {
        $this->connection->beginTransaction();
        $this->assertTrue($this->connection->inTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitReturnsInstanceOfConnection()
    {
        $this->connection->beginTransaction();
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Connection', $this->connection->commit());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testCommitSetsInTransactionAtFalse()
    {
        $this->connection->beginTransaction();
        $this->connection->commit();
        $this->assertFalse($this->connection->inTransaction());
    }

    /**
     * Standalone commit is possible after a SET autocommit=0;
     *
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitWithoutBeginReturnsInstanceOfConnection()
    {
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Connection', $this->connection->commit());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @expectedException \Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Must be connected before you can rollback
     */
    public function testRollbackDisconnectedThrowsException()
    {
        $this->connection->disconnect();
        $this->connection->rollback();
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testRollbackReturnsInstanceOfConnection()
    {
        $this->connection->beginTransaction();
        $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Connection', $this->connection->rollback());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testRollbackSetsInTransactionAtFalse()
    {
        $this->connection->beginTransaction();
        $this->connection->rollback();
        $this->assertFalse($this->connection->inTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @expectedException \Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Must call beginTransaction() before you can rollback
     */
    public function testRollbackWithoutBeginThrowsException()
    {
        $this->connection->rollback();
    }
}
