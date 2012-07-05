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
    Zend\Queue\Message,
    Zend\Queue\Exception;


/**
 * Class testing.  No supported functions.  Also used to disable a Zend_Queue.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Null extends AbstractAdapter
{
    /********************************************************************
     * Queue management functions
     *********************************************************************/

    /**
     * Does a queue already exist?
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function isExists($name)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }


    /**
     * Create a new queue
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function create($name, $timeout=null)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /**
     * Delete a queue and all of it's messages
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function delete($name)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /**
     * Get an array of all available queues
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function getQueues()
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /**
     * Return the approximate number of messages in the queue
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function count(Queue $queue=null)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /********************************************************************
     * Messsage management functions
     *********************************************************************/

    /**
     * Send a message to the queue
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function send($message, Queue $queue=null)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /**
     * Get messages in the queue
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function receive($maxMessages=null, $timeout=null, Queue $queue=null)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
    }

    /**
     * Delete a message from the queue
     *
     * @throws \Zend\Queue\Exception - not supported.
     */
    public function deleteMessage(Message $message)
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . '() is not supported by ' . get_called_class());
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
            'create'        => false,
            'delete'        => false,
            'send'          => false,
            'receive'       => false,
            'deleteMessage' => false,
            'getQueues'     => false,
            'count'         => false,
            'isExists'      => false,
        );
    }
}
