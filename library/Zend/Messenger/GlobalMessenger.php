<?php
/**
 * Phly - PHp LibrarY
 * 
 * @category  Phly
 * @package   \phly\PubSub
 * @copyright Copyright (C) 2008 - Present, Matthew Weier O'Phinney
 * @author    Matthew Weier O'Phinney <mweierophinney@gmail.com> 
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace Zend\Messenger;

/**
 * Static version of Messenger
 */
class GlobalMessenger
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
