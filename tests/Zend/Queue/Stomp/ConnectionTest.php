<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace ZendTest\Queue\Stomp;

use Zend\Queue\Stomp\Client;
use Zend\Queue\Stomp\Connection;
use Zend\Queue\Stomp\Frame;
use PHPUnit_Framework_TestCase as Testcase;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @group      Zend_Queue
 */

class ConnectionSocketOverload extends Connection
{
    /**
     * bypass the open function as it uses fopensocket, inject socket instead
     * @param resource $socket
     */
    public function setSocket($socket)
    {
        $this->_socket = $socket;
    }
}

class ConnectionTest extends TestCase
{
    var $_socket = null;

    public function setUp()
    {
        $tmpfile = tempnam("/tmp", "PHPUnit-");
        $this->_socket = fopen($tmpfile, 'w+');
        unlink($tmpfile);
    }

    public function tearDown()
    {
        fclose($this->_socket);
        $this->_socket = null;
    }

    public function testConnectionException()
    {
        $connection = new ConnectionSocketOverload();
        try {
            $connection->write($frame);
            $this->fail('should have thrown a not connected exception');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $connection->close();
            $this->fail('should have thrown a not connected exception');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testMessageAutoContentLengthOn()
    {
        $frame = new Frame();
        $frame->setCommand('TESTING');
        $frame->setAutoContentLength(true);
        $frame->setBody("hello world");

        $connection = new ConnectionSocketOverload();
        $connection->setSocket($this->_socket);
        $connection->write($frame);
        rewind($this->_socket);
        $this->assertTrue($connection->canRead());
        $test = $connection->read();

        $this->assertEquals("hello world", $test->getBody());
    }

    public function testMessageAutoContentLengthOff()
    {
        $frame = new Frame();
        $frame->setCommand('TESTING');
        $frame->setAutoContentLength(false);
        $frame->setBody("hello world");

        $connection = new ConnectionSocketOverload();
        $connection->setSocket($this->_socket);
        $connection->write($frame);
        rewind($this->_socket);
        $this->assertTrue($connection->canRead());
        $test = $connection->read();

        $this->assertEquals("hello world", $test->getBody());
    }


    public function testByteMessageAutoContentLengthOff()
    {
        $frame = new Frame();
        $frame->setCommand('TESTING');
        $frame->setAutoContentLength(false);
        $frame->setBody('hello ' . Frame::END_OF_FRAME . ' world');

        $connection = new ConnectionSocketOverload();
        $connection->setSocket($this->_socket);
        $connection->write($frame);
        rewind($this->_socket);

        $test = $connection->read();

        $this->assertNotSame('hello ' . Frame::END_OF_FRAME . ' world', $test->getBody());
    }

    public function testByteMessageAutoContentLengthOn()
    {
        $frame = new Frame();
        $frame->setCommand('TESTING');
        $frame->setAutoContentLength(true);
        $frame->setBody('hello ' . Frame::END_OF_FRAME . ' world');

        $connection = new ConnectionSocketOverload();
        $connection->setSocket($this->_socket);
        $connection->write($frame);
        rewind($this->_socket);

        $test = $connection->read();

        $this->assertEquals('hello ' . Frame::END_OF_FRAME . ' world', $test->getBody());
    }
}
