<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_TimeSync
 */

namespace Zend\TimeSync;

use DateTime;

/**
 * Abstract class definition for all timeserver protocols
 *
 * @category  Zend
 * @package   Zend_TimeSync
 */
abstract class AbstractProtocol
{
    /**
     * Holds the current socket connection
     *
     * @var resource
     */
    protected $socket;

    /**
     * Exceptions that might have occurred
     *
     * @var array
     */
    protected $exceptions;

    /**
     * Hostname for timeserver
     *
     * @var string
     */
    protected $timeserver;

    /**
     * Port number for this timeserver
     *
     * @var integer
     */
    protected $port;

    /**
     * Holds information passed/returned from timeserver
     *
     * @var array
     */
    protected $info = array();

    /**
     * Abstract method that prepares the data to send to the timeserver
     *
     * @return string
     */
    abstract protected function prepare();

    /**
     * Abstract method that reads the data returned from the timeserver
     *
     * @return string
     */
    abstract protected function read();

    /**
     * Abstract method that writes data to to the timeserver
     *
     * @param  string $data Data to write
     * @return void
     */
    abstract protected function write($data);

    /**
     * Abstract method that extracts the binary data returned from the timeserver
     *
     * @param  string|array $data Data returned from the timeserver
     * @return integer
     */
    abstract protected function extract($data);

    /**
     * Connect to the specified timeserver.
     *
     * @return void
     * @throws Exception\RuntimeException When the connection failed
     */
    protected function connect()
    {
        $socket = @fsockopen($this->timeserver, $this->port, $errno, $errstr,
                             TimeSync::$options['timeout']);
        if ($socket === false) {
            throw new Exception\RuntimeException('could not connect to ' .
                "'$this->timeserver' on port '$this->port', reason: '$errstr'");
        }

        $this->socket = $socket;
    }

    /**
     * Disconnects from the peer, closes the socket.
     *
     * @return void
     */
    protected function disconnect()
    {
        @fclose($this->socket);
        $this->socket = null;
    }

    /**
     * Return information sent/returned from the timeserver
     *
     * @return array
     */
    public function getInfo()
    {
        if (empty($this->info)) {
            $this->write($this->prepare());
            $this->extract($this->read());
        }

        return $this->info;
    }

    /**
     * Query this timeserver without using the fallback mechanism
     *
     * @return DateTime
     */
    public function getDate()
    {
        $this->write($this->prepare());
        $this->extract($this->read());

        // Apply to the local time the offset obtained from the server
        $info = $this->getInfo();
        $time = (time() + round($info['offset']));

        return new DateTime('@' . $time);
    }
}
