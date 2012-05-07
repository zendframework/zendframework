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

use Zend\Queue\Queue,
    Zend\Queue\Exception,
    Zend\Queue\Message,
    Zend\Db as DB_ns,
    Zend\Db\Adapter\AbstractAdapter as AbstractDBAdapter,
    Zend\Db\Select;

/**
 * Class for using connecting to a Zend_DB-based queuing system
 *
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Db\Expr
 * @uses       \Zend\Db\Select
 * @uses       \Zend\Db\Adapter\AbstractAdapter
 * @uses       \Zend\Db\Table\AbstractRow
 * @uses       \Zend\Queue\Adapter\AdapterAbstract
 * @uses       \Zend\Queue\Adapter\Db\Message
 * @uses       \Zend\Queue\Adapter\Db\Queue
 * @uses       \Zend\Queue\Exception
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Db extends AbstractAdapter
{
    /**
     * @var \Zend\Queue\Adapter\Db\Queue
     */
    protected $_queueTable = null;

    /**
     * @var \Zend\Queue\Adapter\Db\Message
     */
    protected $_messageTable = null;

    /**
     * @var \Zend\Db\Table\AbstractRow
     */
    protected $_messageRow = null;

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

        if (!isset($this->_options['options'][Select::FOR_UPDATE])) {
            // turn off auto update by default
            $this->_options['options'][Select::FOR_UPDATE] = false;
        }

        if (!is_bool($this->_options['options'][Select::FOR_UPDATE])) {
            throw new Exception\InvalidArgumentException('Options array item: \Zend\Db\Select::FOR_UPDATE must be boolean');
        }

        if (isset($this->_options['dbAdapter'])
            && $this->_options['dbAdapter'] instanceof AbstractDBAdapter) {
            $db = $this->_options['dbAdapter'];
        } else {
            $db = $this->_initDBAdapter();
        }

        $this->_queueTable = new Db\Queue(array(
            'db' => $db,
        ));

        $this->_messageTable = new Db\Message(array(
            'db' => $db,
        ));

    }

    /**
     * Initialize DB adapter using 'driverOptions' section of the _options array
     *
     * Throws an exception if the adapter cannot connect to DB.
     *
     * @return \Zend\Db\Adapter\AbstractAdapter
     * @throws \Zend\Queue\Exception
     */
    protected function _initDBAdapter()
    {
        $options = &$this->_options['driverOptions'];
        if (!array_key_exists('type', $options)) {
            throw new Exception\InvalidArgumentException("Configuration array must have a key for 'type' for the database type to use");
        }

        if (!array_key_exists('host', $options)) {
            throw new Exception\InvalidArgumentException("Configuration array must have a key for 'host' for the host to use");
        }

        if (!array_key_exists('username', $options)) {
            throw new Exception\InvalidArgumentException("Configuration array must have a key for 'username' for the username to use");
        }

        if (!array_key_exists('password', $options)) {
            throw new Exception\InvalidArgumentException("Configuration array must have a key for 'password' for the password to use");
        }

        if (!array_key_exists('dbname', $options)) {
            throw new Exception\InvalidArgumentException("Configuration array must have a key for 'dbname' for the database to use");
        }

        $type = $options['type'];
        unset($options['type']);

        try {
            $db = DB_ns\Db::factory($type, $options);
        } catch (DB_ns\Exception $e) {
            throw new Exception\ConnectionException('Error connecting to database: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $db;
    }

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
     * @throws \Zend\Queue\Exception
     */
    public function isExists($name)
    {
        $id = 0;

        try {
            $id = $this->getQueueId($name);
        } catch (Exception $e) {
            return false;
        }

        return ($id > 0);
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
     * @throws \Zend\Queue\Exception - database error
     */
    public function create($name, $timeout = null)
    {
        if ($this->isExists($name)) {
            return false;
        }

        $queue = $this->_queueTable->createRow();
        $queue->queue_name = $name;
        $queue->timeout    = ($timeout === null) ? self::CREATE_TIMEOUT_DEFAULT : (int)$timeout;

        try {
            if ($queue->save()) {
                return true;
            }
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return false;
    }

    /**
     * Delete a queue and all of it's messages
     *
     * Returns false if the queue is not found, true if the queue exists
     *
     * @param  string  $name queue name
     * @return boolean
     * @throws \Zend\Queue\Exception - database error
     */
    public function delete($name)
    {
        $id = $this->getQueueId($name); // get primary key

        // if the queue does not exist then it must already be deleted.
        $list = $this->_queueTable->find($id);
        if (count($list) === 0) {
            return false;
        }
        $queue = $list->current();

        if ($queue instanceof \Zend\Db\Table\AbstractRow) {
            try {
                $queue->delete();
            } catch (\Exception $e) {
                throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        if (array_key_exists($name, $this->_queues)) {
            unset($this->_queues[$name]);
        }

        return true;
    }

    /*
     * Get an array of all available queues
     *
     * Not all adapters support getQueues(), use isSupported('getQueues')
     * to determine if the adapter supports this feature.
     *
     * @return array
     * @throws \Zend\Queue\Exception - database error
     */
    public function getQueues()
    {
        $query = $this->_queueTable->select();
        $query->from($this->_queueTable, array('queue_id', 'queue_name'));

        $this->_queues = array();
        foreach ($this->_queueTable->fetchAll($query) as $queue) {
            $this->_queues[$queue->queue_name] = (int)$queue->queue_id;
        }

        $list = array_keys($this->_queues);

        return $list;
    }

    /**
     * Return the approximate number of messages in the queue
     *
     * @param  \Zend\Queue\Queue $queue
     * @return integer
     * @throws \Zend\Queue\Exception
     */
    public function count(Queue $queue = null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }

        $info  = $this->_messageTable->info();
        $db    = $this->_messageTable->getAdapter();
        $query = $db->select();
        $query->from($info['name'], array(new DB_ns\Expr('COUNT(1)')))
              ->where('queue_id=?', $this->getQueueId($queue->getName()));

        // return count results
        return (int) $db->fetchOne($query);
    }

    /********************************************************************
    * Messsage management functions
     *********************************************************************/

    /**
     * Send a message to the queue
     *
     * @param  string     $message Message to send to the active queue
     * @param  \Zend\Queue\Queue $queue
     * @return \Zend\Queue\Message\Message
     * @throws \Zend\Queue\Exception - database error
     */
    public function send($message, Queue $queue = null)
    {
        if ($this->_messageRow === null) {
            $this->_messageRow = $this->_messageTable->createRow();
        }

        if ($queue === null) {
            $queue = $this->_queue;
        }

        if (is_scalar($message)) {
            $message = (string) $message;
        }
        if (is_string($message)) {
            $message = trim($message);
        }

        if (!$this->isExists($queue->getName())) {
            throw new Exception\QueueNotFoundException('Queue does not exist:' . $queue->getName());
        }

        $msg           = clone $this->_messageRow;
        $msg->queue_id = $this->getQueueId($queue->getName());
        $msg->created  = time();
        $msg->body     = $message;
        $msg->md5      = md5($message);
        // $msg->timeout = ??? @TODO

        try {
            $msg->save();
        } catch (\Exception $e) {
            throw new Exceptioin\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = array(
            'queue' => $queue,
            'data'  => $msg->toArray(),
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
     * @throws \Zend\Queue\Exception - database error
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

        $msgs      = array();
        $info      = $this->_messageTable->info();
        $microtime = microtime(true); // cache microtime
        $db        = $this->_messageTable->getAdapter();

        // start transaction handling
        try {
            if ( $maxMessages > 0 ) { // ZF-7666 LIMIT 0 clause not included.
                $db->beginTransaction();

                $query = $db->select();
                if ($this->_options['options'][Select::FOR_UPDATE]) {
                    // turn on forUpdate
                    $query->forUpdate();
                }
                $query->from($info['name'], array('*'))
                      ->where('queue_id=?', $this->getQueueId($queue->getName()))
                      ->where('handle IS NULL OR timeout+' . (int)$timeout . ' < ' . (int)$microtime)
                      ->limit($maxMessages);

                foreach ($db->fetchAll($query) as $data) {
                    // setup our changes to the message
                    $data['handle'] = md5(uniqid(rand(), true));

                    $update = array(
                        'handle'  => $data['handle'],
                        'timeout' => $microtime,
                    );

                    // update the database
                    $where   = array();
                    $where[] = $db->quoteInto('message_id=?', $data['message_id']);
                    $where[] = 'handle IS NULL OR timeout+' . (int)$timeout . ' < ' . (int)$microtime;

                    $count = $db->update($info['name'], $update, $where);

                    // we check count to make sure no other thread has gotten
                    // the rows after our select, but before our update.
                    if ($count > 0) {
                        $msgs[] = $data;
                    }
                }
                $db->commit();
            }
        } catch (\Exception $e) {
            $db->rollBack();

            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = array(
            'queue'        => $queue,
            'data'         => $msgs,
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
     * @param  \Zend\Queue\Message\Message $message
     * @return boolean
     */
    public function deleteMessage(Message $message)
    {
        $db    = $this->_messageTable->getAdapter();
        $where = $db->quoteInto('handle=?', $message->handle);

        if ($this->_messageTable->delete($where)) {
            return true;
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
     * Get the queue ID
     *
     * Returns the queue's row identifier.
     *
     * @param  string       $name
     * @return integer|null
     * @throws \Zend\Queue\Exception
     */
    protected function getQueueId($name)
    {
        if (array_key_exists($name, $this->_queues)) {
            return $this->_queues[$name];
        }

        $query = $this->_queueTable->select();
        $query->from($this->_queueTable, array('queue_id'))
              ->where('queue_name=?', $name);

        $queue = $this->_queueTable->fetchRow($query);

        if ($queue === null) {
            throw new Exception\QueueNotFoundException('Queue does not exist: ' . $name);
        }

        $this->_queues[$name] = (int)$queue->queue_id;

        return $this->_queues[$name];
    }
}
