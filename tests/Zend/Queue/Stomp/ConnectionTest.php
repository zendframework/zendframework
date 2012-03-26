<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Queue\Stomp;

use Zend\Queue\Stomp\Client,
    Zend\Queue\Stomp\Connection,
    Zend\Queue\Stomp\Frame,
    PHPUnit_Framework_TestCase as Testcase;

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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
