<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace ZendTest\Queue\Custom;

use Zend\Queue as QueueNS;

/**
 * This class uses the SLP_ArrayIterator
 * We are interested in overriding unset() to auto delete the message
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 */
class Messages extends \Zend\Queue\Message\MessageIterator implements \ArrayAccess
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
            throw new QueueNS\Exception('array configuration must have $config[\'data\'] = array');
        }

        // load the message class
        $class = $this->_messageClass;

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
        if (! $value instanceof Message) {
            throw new QueueNS\Exception(
                '$value must be a child or an instance of \ZendTest\Queue\Custom\Messag'
            );
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
            throw new QueueNS\Exception('Cannot delete message after serialization');
        }

        $this->_data[$offset]->delete(); // \ZendTest\Queue\Custom\Message added this function
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
