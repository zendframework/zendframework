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
 */

namespace ZendTest\Queue\Custom;

/**
 * Class for custom messages
 *
 * We want be able to delete messages and we to serialize objects
 * via a getBody() and setBody()
 *
 * This is different that doing the serialization directly next to the object
 * because you may want to update getBody() and setBody() to do a json
 * conversion instead of a php serialize
 *
 * You could also just overload the Zend_Queue::send() and Zend_Queue::receive()
 * functions to do the serialization/json encoding, but I wanted to give an
 * good example that overloaded near everything except for the adapter.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 */
class Message extends \Zend\Queue\Message
{
    /**
     * We adjusted the constructor to accept both an array and an object.
     */
    public function __construct($mixed) {
        // we still have to support the code in \Zend\Queue\Queue::receive that
        // passes in an array
        if (is_array($mixed)) {
            parent::__construct($mixed);
        } elseif (is_object($mixed)) {
            $this->setBody($mixed);
            $this->_connected = false;
        }
    }

    /**
     * We need to get the underlying body as a string
     *
     * @return string
     */
    public function __toString() {
        return $this->_data['body'];
    }

    /**
     * Sets the message body
     *
     * @param serializable $mixed
     */
    public function setBody($mixed)
    {
        $this->_data['body'] = serialize($mixed);
    }

    /**
     * Gets the message body
     *
     * @return $mixed
     */
    public function getBody()
    {
        return unserialize($this->_data['body']);
    }

    /**
     * Deletes the message.
     *
     * Note you cannot be disconnected from queue.
     *
     * $throw is set to to true, because most of the time you want to know if
     * there is an error.  However, in Messages::__destruct() exceptions
     * cannot be thrown.
     *
     * These does not create a circular reference loop. Because deleteMessage
     * asks the queue service to delete the message, the message located here
     * is NOT deleted.
     *
     * @param boolean $throw defaults to true.  Throw a message if there is an error
     *
     * @throws \Zend\Queue\Exception if not connected
     */
    public function delete($throw = true)
    {
        if ($this->_connected) {
            if ($this->getQueue()->getAdapter()->isSupported('deleteMessage')) {
                $this->getQueue()->deleteMessage($this);
            }
        } elseif ($throw) {
            throw new \Zend\Queue\Exception('Disconnected from queue.  Cannot delete message from queue.');
        }
    }
}
