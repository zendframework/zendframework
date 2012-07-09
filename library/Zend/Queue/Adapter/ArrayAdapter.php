<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace Zend\Queue\Adapter;

use Zend\Queue\Exception;
use Zend\Queue\Message;
use Zend\Queue\Queue;

/**
 * Class for using a standard PHP array as a queue
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 */
class ArrayAdapter extends AbstractAdapter
{
    /**
     * @var array
     */
    protected $_data = array();


    /********************************************************************
    * Queue management functions
     *********************************************************************/

    /**
     * Does a queue already exist?
     *
     * Throws an exception if the adapter cannot determine if a queue exists.
     * use isSupported('isExists') to determine if an adapter can test for
     * queue existance.
     *
     * @param  string $name
     * @return boolean
     */
    public function isExists($name)
    {
        return array_key_exists($name, $this->_data);
    }

    /**
     * Create a new queue
     *
     * Visibility timeout is how long a message is left in the queue "invisible"
     * to other readers.  If the message is acknowleged (deleted) before the
     * timeout, then the message is deleted.  However, if the timeout expires
     * then the message will be made available to other queue readers.
     *
     * @param  string  $name    queue name
     * @param  integer $timeout default visibility timeout
     * @return boolean
     */
    public function create($name, $timeout=null)
    {
        if ($this->isExists($name)) {
            return false;
        }
        if ($timeout === null) {
            $timeout = self::CREATE_TIMEOUT_DEFAULT;
        }
        $this->_data[$name] = array();

        return true;
    }

    /**
     * Delete a queue and all of it's messages
     *
     * Returns false if the queue is not found, true if the queue exists
     *
     * @param  string  $name queue name
     * @return boolean
     */
    public function delete($name)
    {
        $found = isset($this->_data[$name]);

        if ($found) {
            unset($this->_data[$name]);
        }

        return $found;
    }

    /**
     * Get an array of all available queues
     *
     * Not all adapters support getQueues(), use isSupported('getQueues')
     * to determine if the adapter supports this feature.
     *
     * @return array
     */
    public function getQueues()
    {
        return array_keys($this->_data);
    }

    /**
     * Return the approximate number of messages in the queue
     *
     * @param  \Zend\Queue\Queue $queue
     * @return integer
     * @throws \Zend\Queue\Exception
     */
    public function count(Queue $queue=null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }

        if (!isset($this->_data[$queue->getName()])) {
            throw new Exception\QueueNotFoundException('Queue does not exist');
        }

        return count($this->_data[$queue->getName()]);
    }

    /********************************************************************
    * Messsage management functions
     *********************************************************************/

    /**
     * Send a message to the queue
     *
     * @param  string     $message Message to send to the active queue
     * @param  \Zend\Queue\Queue $queue
     * @return \Zend\Queue\Message
     * @throws \Zend\Queue\Exception
     */
    public function send($message, Queue $queue=null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }

        if (!$this->isExists($queue->getName())) {
            throw new Exception\QueueNotFoundException('Queue does not exist:' . $queue->getName());
        }

        // create the message
        $data = array(
            'message_id' => md5(uniqid(rand(), true)),
            'body'       => $message,
            'md5'        => md5($message),
            'handle'     => null,
            'created'    => time(),
            'queue_name' => $queue->getName(),
        );

        // add $data to the "queue"
        $this->_data[$queue->getName()][] = $data;

        $options = array(
            'queue' => $queue,
            'data'  => $data,
        );
        $classname = $queue->getMessageClass();
        return new $classname($options);
    }

    /**
     * Get messages in the queue
     *
     * @param  integer    $maxMessages  Maximum number of messages to return
     * @param  integer    $timeout      Visibility timeout for these messages
     * @param  \Zend\Queue\Queue $queue
     * @return \Zend\Queue\Message\MessageIterator
     */
    public function receive($maxMessages = null, $timeout = null, Queue $queue = null)
    {
        if ($maxMessages === null) {
            $maxMessages = 1;
        }
        if ($timeout === null) {
            $timeout = self::RECEIVE_TIMEOUT_DEFAULT;
        }
        if ($queue === null) {
            $queue = $this->_queue;
        }

        $data       = array();
        if ($maxMessages > 0) {
            $start_time = microtime(true);

            $count = 0;
            $temp = &$this->_data[$queue->getName()];
            foreach ($temp as $key=>&$msg) {
                // count check has to be first, as someone can ask for 0 messages
                // ZF-7650
                if ($count >= $maxMessages) {
                    break;
                }

                if (($msg['handle'] === null)
                    || ($msg['timeout'] + $timeout < $start_time)
                ) {
                    $msg['handle']  = md5(uniqid(rand(), true));
                    $msg['timeout'] = microtime(true);
                    $data[] = $msg;
                    $count++;
                }

            }
        }

        $options = array(
            'queue'        => $queue,
            'data'         => $data,
            'messageClass' => $queue->getMessageClass(),
        );
        $classname = $queue->getMessageSetClass();
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
        // load the queue
        $queue = &$this->_data[$message->queue_name];

        foreach ($queue as $key => &$msg) {
            if ($msg['handle'] == $message->handle) {
                unset($queue[$key]);
                return true;
            }
        }

        return false;
    }

    /********************************************************************
     * Supporting functions
     *********************************************************************/

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
            'create'        => true,
            'delete'        => true,
            'send'          => true,
            'receive'       => true,
            'deleteMessage' => true,
            'getQueues'     => true,
            'count'         => true,
            'isExists'      => true,
        );
    }

    /********************************************************************
    * Functions that are not part of the \Zend\Queue\Adapter\AdapterAbstract
     *********************************************************************/

    /**
     * serialize
     */
    public function __sleep()
    {
        return array('_data');
    }

    /*
     * These functions are debug helpers.
     */

    /**
     * returns underlying _data array
     * $queue->getAdapter()->getData();
     *
     * @return $this;
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * sets the underlying _data array
     * $queue->getAdapter()->setData($data);
     *
     * @param $data array
     * @return $this;
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }
}
