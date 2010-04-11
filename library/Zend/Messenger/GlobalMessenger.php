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
 * Static version of Messenger
 *
 * @uses       Zend\Messenger\StaticDelivery
 * @category   Zend
 * @package    Zend_Messenger
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GlobalMessenger implements StaticDelivery
{
    /**
     * @var Messenger
     */
    protected static $_instance;

    /**
     * Retrieve messenger instance
     * 
     * @return Messenger
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::setInstance(new Messenger());
        }
        return self::$_instance;
    }

    /**
     * Set messenger instance
     * 
     * @param  Messenger|null $provider 
     * @return void
     */
    public static function setInstance(Messenger $messenger = null)
    {
        self::$_instance = $messenger;
    }

    /**
     * Notify all handlers for a given topic
     * 
     * @param  string $topic 
     * @param  mixed $args All arguments besides the topic are passed as arguments to the handler
     * @return void
     */
    public static function notify($topic, $args = null)
    {
        $messenger = self::getInstance();
        return $messenger->notify($topic, $args);
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
    public static functiomespace \Zend\Filter;n notifyUntil($callback, $topic, $args = null)
    {
        $messenger = self::getInstance();
        $args = func_get_args();
        return call_user_func_array(array($messenger, 'notifyUntil'), $args);
    }

    /**
     * Attach a handler to a topic
     * 
     * @param  string $topic 
     * @param  string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return Handler Pub-Sub handle (to allow later unsubscribe)
     */
    public static function attach($topic, $context, $handler = null)
    {
        $messenger = self::getInstance();
        return $messenger->attach($topic, $context, $handler);
    }

    /**
     * Detach a handler from a topic 
     * 
     * @param  Handler $handle 
     * @return bool Returns true if topic and handle found, and unsubscribed; returns false if either topic or handle not found
     */
    public static function detach(Handler $handler)
    {
        $messenger = self::getInstance();
        return $messenger->detach($handler);
    }

    /**
     * Retrieve all registered topics
     * 
     * @return array
     */
    public static function getTopics()
    {
        $messenger = self::getInstance();
        return $messenger->getTopics();
    }

    /**
     * Retrieve all handlers for a given topic
     * 
     * @param  string $topic 
     * @return Handler[]
     */
    public static function getHandlers($topic)
    {
        $messenger = self::getInstance();
        return $messenger->getHandlers($topic);
    }

    /**
     * Clear all handlers for a given topic
     * 
     * @param  string $topic 
     * @return void
     */
    public static function clearHandlers($topic)
    {
        $messenger = self::getInstance();
        return $messenger->clearHandlers($topic);
    }
}
