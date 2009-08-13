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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/** TestHelp.php */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Queue_Stomp_Frame */
require_once 'Zend/Queue/Stomp/Frame.php';

/** Zend_Queue_Stomp_Client */
require_once 'Zend/Queue/Stomp/Client.php';

/** Zend_Queue_Stomp_Client_Interface */
require_once 'Zend/Queue/Stomp/Client/Connection.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_Stomp_Connection_Mock
    extends Zend_Queue_Stomp_Client_Connection
{
    /**
     * open() opens a socket to the Stomp server
     *
     * @param array $config ('scheme', 'host', 'port')
     * @return true;
     */
    public function open($scheme, $host, $port)
    {
        if ( $port == 0 )  return false;
        return true;
    }

    public function close($destructor = false)
    {
    }

    /**
     * Check whether we are connected to the server
     *
     * @return true
     * @throws Zend_Queue_Exception
     */
    public function ping()
    {
        return true;
    }

    /**
     * write a frame to the stomp server
     *
     * @example $response = $client->write($frame)->read();
     *
     * @param Zend_Queue_Stom_Frame $frame
     * @return $this
     */
    public function write(Zend_Queue_Stomp_FrameInterface $frame)
    {
        $this->_buffer[] = $frame;
    }

    /**
     * tests the socket to see if there is data for us
     */
    public function canRead()
    {
        return count($this->_buffer) > 0;
    }

    /**
     * reads in a frame from the socket or returns false.
     *
     * @return Zend_Queue_Stomp_Frame|false
     * @throws Zend_Queue_Exception
     */
    public function read()
    {
        if (! $this->canRead()) return false;
        return array_shift($this->_buffer);
    }
}

class Zend_Queue_Stomp_ClientTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $client = new Zend_Queue_Stomp_Client('tcp', 'localhost', '11232', 'Zend_Queue_Stomp_Connection_Mock');
        $this->assertTrue($client->getConnection() instanceof Zend_Queue_Stomp_Client_ConnectionInterface);
    }

    public function testAddConnection()
    {
        $client = new Zend_Queue_Stomp_Client();
        $client->addConnection('tcp', 'localhost', '11232', 'Zend_Queue_Stomp_Connection_Mock');
        $this->assertTrue($client->getConnection() instanceof Zend_Queue_Stomp_Client_ConnectionInterface);

        $client = new Zend_Queue_Stomp_Client();
        $this->assertFalse($client->addConnection('tcp', 'localhost', 0, 'Zend_Queue_Stomp_Connection_Mock'));
    }

    public function testGetAndSetConnection()
    {
        $connection = new Zend_Queue_Stomp_Connection_Mock('tcp', 'localhost', '11232');

        $client = new Zend_Queue_Stomp_Client();
        $this->assertTrue($client->setConnection($connection) instanceof Zend_Queue_Stomp_Client);

        $try = $client->getConnection();
        $this->assertEquals($connection, $try);
    }

    public function testSendAndReceive()
    {
        $frame = new Zend_Queue_Stomp_Frame();
        $frame->setCommand('testing');
        $frame->setHeader('testing',1);
        $frame->setBody('hello world');

        $client = new Zend_Queue_Stomp_Client();
        $client->addConnection('tcp', 'localhost', '11232', 'Zend_Queue_Stomp_Connection_Mock');

        $client->send($frame);
        $this->assertTrue($client->canRead());
        $test = $client->receive();

        $this->assertEquals('testing', $test->getCommand());
        $this->assertEquals(1, $test->getHeader('testing'));
        $this->assertEquals('hello world', $test->getBody());
    }
}
