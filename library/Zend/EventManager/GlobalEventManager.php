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
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\EventManager;

use Zend\Stdlib\CallbackHandler,
    Zend\Stdlib\PriorityQueue,
    ArrayObject;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification 
 * system for your objects.
 *
 * @category   Zend
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GlobalEventManager
{
    /**
     * @var EventCollection
     */
    protected static $events;

    /**
     * Set the event collection on which this will operate
     * 
     * @param  null|EventCollection $events 
     * @return void
     */
    public static function setEventCollection(EventCollection $events = null)
    {
        static::$events = $events;
    }

    /**
     * Get event collection on which this operates
     * 
     * @return void
     */
    public static function getEventCollection()
    {
        if (null === static::$events) {
            static::setEventCollection(new EventManager());
        }
        return static::$events;
    }

    /**
     * Trigger an event
     * 
     * @param  string $event 
     * @param  object|string $context 
     * @param  array|object $argv 
     * @return ResponseCollection
     */
    public static function trigger($event, $context, $argv = array())
    {
        return static::getEventCollection()->trigger($event, $context, $argv);
    }

    /**
     * Trigger listeenrs until return value of one causes a callback to evaluate 
     * to true.
     * 
     * @param  string $event 
     * @param  string|object $context 
     * @param  array|object $argv 
     * @param  callback $callback 
     * @return ResponseCollection
     */
    public static function triggerUntil($event, $context, $argv, $callback)
    {
        return static::getEventCollection()->triggerUntil($event, $context, $argv, $callback);
    }

    /**
     * Attach a listener to an event
     * 
     * @param  string $event 
     * @param  callback $callback 
     * @param  int $priority 
     * @return CallbackHandler
     */
    public static function attach($event, $callback, $priority = 1)
    {
        return static::getEventCollection()->attach($event, $callback, $priority);
    }

    /**
     * Detach a callback from a listener
     * 
     * @param  CallbackHandler $listener 
     * @return bool
     */
    public static function detach(CallbackHandler $listener)
    {
        return static::getEventCollection()->detach($listener);
    }

    /**
     * Retrieve list of events this object manages
     * 
     * @return array
     */
    public static function getEvents()
    {
        return static::getEventCollection()->getEvents();
    }

    /**
     * Retrieve all listeners for a given event
     * 
     * @param  string $event 
     * @return PriorityQueue|array
     */
    public static function getListeners($event)
    {
        return static::getEventCollection()->getListeners($event);
    }

    /**
     * Clear all listeners for a given event
     * 
     * @param  string $event 
     * @return void
     */
    public static function clearListeners($event)
    {
        return static::getEventCollection()->clearListeners($event);
    }
}
