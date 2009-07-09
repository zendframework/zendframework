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
 * @subpackage Custom
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Iterator.php 14781 2009-04-09 07:07:24Z danlo $
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Custom
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This class uses the SLP_ArrayIterator
 * We are interested in overriding unset() to auto delete the message
 */

/** Zend_Queue_Message_Iterator */
require_once('Zend/Queue/Message/Iterator.php');

class Custom_Messages
extends Zend_Queue_Message_Iterator
implements ArrayAccess
{
    /**
     * Constructor
     *
     * @param array $config ('queue', 'messageClass', 'data'=>array());
     */
    public function __construct(array $config=array())
    {
        if (isset($config['queue'])) {
            $this->_queue = $config['queue'];
            $this->_queueClass = get_class($this->_queue);
            $this->_connected = true;
        } else {
            $this->_connected = false;
        }

        if (isset($config['messageClass'])) {
            $this->_messageClass = $config['messageClass'];
        }

        if (isset($config['data']) && ! is_array($config['data'])) {
            /**
             * @see Zend_Queue_Exception
             */
            require_once 'Zend/Queue/Exception.php';
            throw new Zend_Queue_Exception('array configuration must have $config[\'data\'] = array');
        }

        // load the message class
        $class = $this->_messageClass;
        Zend_Loader::loadClass($class);

        if (isset($config['data'])) {
            // for each of the messages
            foreach($config['data'] as $i => $data) {
                // construct the message parameters
                $message = array('data' => $data);

                // If queue has not been set, then use the default.
                if (empty($message['queue'])) {
                    $message['queue'] = $this->_queue;
                }

                // construct the message and add it to _data[];
                $this->_data[] = new $class($message);
            }
        }
    }

    /**
     * Our destruct will delete all the messages in the queue
     *
     * Notice: if anything throws a message we are doomed.
     * You cannot throw an error in an destructor
     */
    public function __destruct()
    {
        if ($this->_connected) {
            foreach ($this->_data as $i => $value) {
                $value->delete(false);
            }
        } else {
            unset($this->_data);
        }
    }

    /*
     * ArrayIterator
     */

    /**
     * @see SPL ArrayIterator::append
     */
    public function append($value) {
        $this->_data[] = $value;
    }

    /*
     * ArrayAccess
     */

    /**
     * @see SPL ArrayAccess::offsetSet
     */
    public function offsetSet($offset, $value) {
        if (! $value instanceof Custom_Message) {
            $msg = '$value must be a child or an instance of Custom_Messag';
            /**
             * @see Zend_Queue_Exception
             */
            require_once 'Zend/Queue/Exception.php';
            throw new Zend_Queue_Exception($msg);
        }

        $this->_data[$offset] = $value;
        return $value;
    }

    /**
     * @see SPL ArrayAccess::offsetGet
     */
    public function offsetGet($offset) {
        return $this->_data[$offset];
    }

    /**
     * @see SPL ArrayAccess::offsetUnset
     */
    public function offsetUnset($offset) {
        if (! $this->_connected) {
            $msg = 'Cannot delete message after serialization';
            /**
             * @see Zend_Queue_Exception
             */
            require_once 'Zend/Queue/Exception.php';
            throw new Zend_Queue_Exception($msg);
        }

        $this->_data[$offset]->delete(); // Custom_Message added this function
        unset($this->_data[$offset]);
    }

    /**
     * @see SPL ArrayAccess::offsetExists
     */
    public function offsetExists($offset) {
        return isSet($this->_data[$offset]);
    }

    /*
     * SeekableIterator implementation
     */

    /**
     * @see SPL SeekableIterator::seek
     */
    public function seek($index) {
        $this->_pointer = $index;
    }
}
