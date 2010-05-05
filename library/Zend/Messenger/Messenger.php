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
 * @package    Zend_Messenger
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Messenger;

/**
 * Messenger: per-instance message system
 *
 * Use Messenger when you want to create a per-instance plugin system for your 
 * objects.
 *
 * @uses       Zend\Messenger\Delivery
 * @category   Zend
 * @package    Zend_Messenger
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Messenger implements Delivery
{
    /**
     * Subscribed topics and their handles
     */
    protected $_topics = array();

    /**
     * Publish to all handlers for a given topic
     * 
     * @param  string $topic 
     * @param  mixed $argv All arguments besides the topic are passed as arguments to the handler
     * @return void
     */
    public function notify($topic, $argv = null)
    {
        if (!is_array($argv)) {
            $argv = func_get_args();
            $argv = array_slice($argv, 1);
        }
        return $this->notifyUntil(function(){
            return false;
        }, $topic, $argv);
    }

    /**
     * Notify subscribers until return value of one causes a callback to 
     * evaluate to true
     *
     * Publishes subscribers until the provided callback evaluates the return 
     * value of one as true, or until all subscribers have been executed.
     * 
     * @param  Callable $callback 
     * @param  string $topic 
     * @param  mixed $argv All arguments besides the topic are passed as arguments to the handler
     * @return mixed
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function notifyUntil($callback, $topic, $argv = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        if (empty($this->_topics[$topic])) {
            return;
        }

        $return = null;
        if (!is_array($argv)) {
            $argv   = func_get_args();
            $argv   = array_slice($argv, 2);
        }
        foreach ($this->_topics[$topic] as $handle) {
            $return = $handle->call($argv);
            if (call_user_func($callback, $return)) {
                break;
            }
        }
        return $return;
    }

    /**
     * Subscribe to a topic
     * 
     * @param  string $topic 
     * @param  string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return Handle Pub-Sub handle (to allow later unsubscribe)
     */
    public function attach($topic, $context, $handler = null)
    {
        if (empty($this->_topics[$topic])) {
            $this->_topics[$topic] = array();
        }
        $handle = new Handler($topic, $context, $handler);
        if ($index = array_search($handle, $this->_topics[$topic])) {
            return $this->_topics[$topic][$index];
        }
        $this->_topics[$topic][] = $handle;
        return $handle;
    }

    /**
     * Unsubscribe a handler from a topic 
     * 
     * @param  Handle $handle 
     * @return bool Returns true if topic and handle found, and unsubscribed; returns false if either topic or handle not found
     */
    public function detach(Handler $handle)
    {
        $topic = $handle->getTopic();
        if (empty($this->_topics[$topic])) {
            return false;
        }
        if (false === ($index = array_search($handle, $this->_topics[$topic]))) {
            return false;
        }
        unset($this->_topics[$topic][$index]);
        return true;
    }

    /**
     * Retrieve all registered topics
     * 
     * @return array
     */
    public function getTopics()
    {
        return array_keys($this->_topics);
    }

    /**
     * Retrieve all handlers for a given topic
     * 
     * @param  string $topic 
     * @return array Array of Handle objects
     */
    public function getHandlers($topic)
    {
        if (empty($this->_topics[$topic])) {
            return array();
        }
        return $this->_topics[$topic];
    }

    /**
     * Clear all handlers for a given topic
     * 
     * @param  string $topic 
     * @return void
     */
    public function clearHandlers($topic)
    {
        if (!empty($this->_topics[$topic])) {
            unset($this->_topics[$topic]);
        }
    }
}
