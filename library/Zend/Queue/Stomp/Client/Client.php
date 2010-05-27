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
 * @subpackage Stomp
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Queue\Stomp\Client;

/**
 * The Stomp client interacts with a Stomp server.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Stomp
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Client
{
    /**
     * Array of $client \Zend\Queue\Stomp\Client\ConnectionInterface
     *
     * @var array
     */
    protected $_connection;

    /**
     * Add a server to connections
     *
     * @param string scheme
     * @param string host
     * @param integer port
     */
    public function __construct(
        $scheme = null, $host = null, $port = null,
        $connectionClass = '\Zend\Queue\Stomp\Client\Connection',
        $frameClass = '\Zend\Queue\Stomp\Frame'
    ) {
        if (($scheme !== null)
            && ($host !== null)
            && ($port !== null)
        ) {
            $this->addConnection($scheme, $host, $port, $connectionClass);
            $this->getConnection()->setFrameClass($frameClass);
        }
    }

    /**
     * Shutdown
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->getConnection()) {
            $this->getConnection()->close(true);
        }
    }

    /**
     * Add a connection to this client.
     *
     * Attempts to add this class to the client.  Returns a boolean value
     * indicating success of operation.
     *
     * You cannot add more than 1 connection to the client at this time.
     *
     * @param string  $scheme ['tcp', 'udp']
     * @param string  host
     * @param integer port
     * @param string  class - create a connection with this class; class must support \Zend\Queue\Stomp\Client\ConnectionInterface
     * @return boolean
     */
    public function addConnection($scheme, $host, $port, $class = '\Zend\Queue\Stomp\Client\Connection')
    {
        $connection = new $class();

        if ($connection->open($scheme, $host, $port)) {
            $this->setConnection($connection);
            return true;
        }

        $connection->close();
        return false;
    }

    /**
     * Set client connection
     *
     * @param \Zend\Queue\Stomp\Client\ConnectionInterface $connection
     * @return void
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->_connection = $connection;
        return $this;
    }

    /**
     * Get client connection
     *
     * @return \Zend\Queue\Stomp\Client\ConnectionInterface|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Send a stomp frame
     *
     * Returns true if the frame was successfully sent.
     *
     * @param \Zend\Queue\Stomp\FrameInterface $frame
     * @return boolean
     */
    public function send(\Zend\Queue\Stomp\FrameInterface $frame)
    {
        $this->getConnection()->write($frame);
        return $this;
    }

    /**
     * Receive a frame
     *
     * Returns a frame or false if none were to be read.
     *
     * @return \Zend\Queue\Stomp\FrameInterface|boolean
     */
    public function receive()
    {
        return $this->getConnection()->read();
    }

    /**
     * canRead()
     *
     * @return boolean
     */
    public function canRead()
    {
        return $this->getConnection()->canRead();
    }

    /**
     * creates a frame class
     *
     * @return \Zend\Queue\Stomp\FrameInterface
     */
    public function createFrame()
    {
        return $this->getConnection()->createFrame();
    }
}
