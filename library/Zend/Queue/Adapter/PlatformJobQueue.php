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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Queue\Adapter;
use ZendAPI_Job,
    ZendAPI_Queue,
    Zend\Queue\Queue,
    Zend\Queue\Message,
    Zend\Queue\Exception;

/**
 * Zend Platform JobQueue adapter
 *
 * @uses       \ZendAPI_Queue
 * @uses       \ZendAPI_Job
 * @uses       \Zend\Queue\Adapter\AdapterAbstract
 * @uses       \Zend\Queue\Queue
 * @uses       \Zend\Queue\Exception
 * @uses       \Zend\Queue\Message\Message
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PlatformJobQueue extends AbstractAdapter
{
    /**
     * @var \ZendAPI_Queue
     */
    protected $_zendQueue;

    /**
     * Constructor
     *
     * @param  array|\Zend\Config\Config $options
     * @param  \Zend\Queue\Queue|null $queue
     * @return void
     */
    public function __construct($options, Queue $queue = null)
    {
        parent::__construct($options, $queue);

        if (!extension_loaded("jobqueue_client")) {
            throw new Exception\ExtensionNotLoadedException('Platform Job Queue extension does not appear to be loaded');
        }

        if (! isset($this->_options['daemonOptions'])) {
            throw new Exception\InvalidArgumentException('Job Queue host and password should be provided');
        }

        $options = $this->_options['daemonOptions'];

        if (!array_key_exists('host', $options)) {
            throw new Exception\InvalidArgumentException('Platform Job Queue host should be provided');
        }
        if (!array_key_exists('password', $options)) {
            throw new Exception\InvalidArgumentException('Platform Job Queue password should be provided');
        }

        $this->_zendQueue = new ZendAPI_Queue($options['host']);

        if (!$this->_zendQueue) {
            throw new Exception\ConnectionException('Platform Job Queue connection failed');
        }
        if (!$this->_zendQueue->login($options['password'])) {
            throw new Exception\ConnectionException('Job Queue login failed');
        }

        if ($this->_queue) {
            $this->_queue->setMessageClass('\Zend\Queue\Message\PlatformJob');
        }
    }

    /********************************************************************
     * Queue management functions
     ********************************************************************/

    /**
     * Does a queue already exist?
     *
     * @param  string $name
     * @return boolean
     * @throws \Zend\Queue\Exception (not supported)
     */
    public function isExists($name)
    {
        throw new Exception\UnsupportedMethodCallException('isExists() is not supported in this adapter');
    }

    /**
     * Create a new queue
     *
     * @param  string  $name    queue name
     * @param  integer $timeout default visibility timeout
     * @return void
     * @throws \Zend\Queue\Exception
     */
    public function create($name, $timeout=null)
    {
        throw new Exception\UnsupportedMethodCallException('create() is not supported in ' . get_class($this));
    }

    /**
     * Delete a queue and all of its messages
     *
     * @param  string $name queue name
     * @return void
     * @throws \Zend\Queue\Exception
     */
    public function delete($name)
    {
        throw new Exception\UnsupportedMethodCallException('delete() is not supported in ' . get_class($this));
    }

    /**
     * Get an array of all available queues
     *
     * @return void
     * @throws \Zend\Queue\Exception
     */
    public function getQueues()
    {
        throw new Exception\UnsupportedMethodCallException('getQueues() is not supported in this adapter');
    }

    /**
     * Return the approximate number of messages in the queue
     *
     * @param  \Zend\Queue\Queue|null $queue
     * @return integer
     */
    public function count(Queue $queue = null)
    {
        if ($queue !== null) {
            throw new Exception\UnsupportedMethodCallException('Queue parameter is not supported');
        }

        return $this->_zendQueue->getNumOfJobsInQueue();
    }

    /********************************************************************
     * Messsage management functions
     ********************************************************************/

    /**
     * Send a message to the queue
     *
     * @param  array|\ZendAPI_Job $message Message to send to the active queue
     * @param  \Zend\Queue\Queue $queue     Not supported
     * @return \Zend\Queue\Message\Message
     * @throws \Zend\Queue\Exception
     */
    public function send($message, Queue $queue = null)
    {
        if ($queue !== null) {
            throw new Exception\UnsupportedMethodCallException('Queue parameter is not supported');
        }

        // This adapter can work only for this message type
        $classname = $this->_queue->getMessageClass();

        if ($message instanceof ZendAPI_Job) {
            $message = array('data' => $message);
        }

        $zendApiJob = new $classname($message);

        // Unfortunately, the Platform JQ API is PHP4-style...
        $platformJob = $zendApiJob->getJob();

        $jobId = $this->_zendQueue->addJob($platformJob);

        if (!$jobId) {
            throw new Exception\RuntimeException('Failed to add a job to queue: '
                . $this->_zendQueue->getLastError());
        }

        $zendApiJob->setJobId($jobId);
        return $zendApiJob;
    }

    /**
     * Get messages in the queue
     *
     * @param  integer    $maxMessages    Maximum number of messages to return
     * @param  integer    $timeout        Ignored
     * @param  \Zend\Queue\Queue $queue   Not supported
     * @throws \Zend\Queue\Exception
     * @return ArrayIterator
     */
    public function receive($maxMessages = null, $timeout = null, Queue $queue = null)
    {
        if ($maxMessages === null) {
            $maxMessages = 1;
        }

        if ($queue !== null) {
            throw new Exception\UnsupportedMethodCallException('Queue shouldn\'t be set');
        }

        $jobs = $this->_zendQueue->getJobsInQueue(null, $maxMessages, true);

        $options = array(
            'queue'        => $this->_queue,
            'data'         => $jobs,
            'messageClass' => $this->_queue->getMessageClass(),
        );
        $classname = $this->_queue->getMessageSetClass();
        return new $classname($options);
    }

    /**
     * Delete a message from the queue
     *
     * Returns true if the message is deleted, false if the deletion is
     * unsuccessful.
     *
     * @param  \Zend\Queue\Message $message
     * @return boolean
     * @throws \Zend\Queue\Exception
     */
    public function deleteMessage(Message $message)
    {
        if (get_class($message) != $this->_queue->getMessageClass()) {
            throw new Exception\DomainException(
                'Failed to remove job from the queue; only messages of type '
                . '\Zend\Queue\Message\PlatformJob may be used'
            );
        }

        return $this->_zendQueue->removeJob($message->getJobId());
    }

    public function isJobIdExist($id)
    {
         return (($this->_zendQueue->getJob($id))? true : false);
    }

    /********************************************************************
     * Supporting functions
     ********************************************************************/

    /**
     * Return a list of queue capabilities functions
     *
     * $array['function name'] = true or false
     * true is supported, false is not supported.
     *
     * @param  string $name
     * @return array
     */
    public function getCapabilities()
    {
         return array(
            'create'                => false,
            'delete'                => false,
            'getQueues'             => false,
            'isExists'              => false,
            'count'                 => true,
            'send'                  => true,
            'receive'               => true,
            'deleteMessage'         => true,
        );
    }

    /********************************************************************
     * Functions that are not part of the \Zend\Queue\Adapter\AdapterAbstract
     ********************************************************************/

    /**
     * Serialize
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_options');
    }

    /**
     * Unserialize
     *
     * @return void
     */
    public function __wakeup()
    {
        $options = $this->_options['daemonOptions'];

        $this->_zendQueue = new ZendAPI_Queue($options['host']);

        if (!$this->_zendQueue) {
            throw new Exception\ConnectionException('Platform Job Queue connection failed');
        }
        if (!$this->_zendQueue->login($options['password'])) {
            throw new Exception\ConnectionException('Job Queue login failed');
        }
    }
}
