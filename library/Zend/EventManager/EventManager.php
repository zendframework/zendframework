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
class EventManager implements EventCollection
{
    /**
     * Subscribed events and their handlers
     * @var array Array of PriorityQueue objects
     */
    protected $events = array();

    /**
     * @var string Class representing the event being emitted
     */
    protected $eventClass = 'Zend\EventManager\Event';

    /**
     * Identifier, used to pull static signals from StaticEventManager
     * @var null|string
     */
    protected $identifier;

    /**
     * Static connections
     * @var false|null|StaticEventCollection
     */
    protected $staticConnections = null;

    /**
     * Constructor
     *
     * Allows optionally specifying an identifier to use to pull signals from a 
     * StaticEventManager.
     * 
     * @param  null|string|int $identifier 
     * @return void
     */
    public function __construct($identifier = null)
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the event class to utilize
     * 
     * @param  string $class 
     * @return EventManager
     */
    public function setEventClass($class)
    {
        $this->eventClass = $class;
        return $this;
    }

    /**
     * Set static connections container
     * 
     * @param  null|StaticEventCollection $connections 
     * @return void
     */
    public function setStaticConnections(StaticEventCollection $connections = null)
    {
        if (null === $connections) {
            $this->staticConnections = false;
        } else {
            $this->staticConnections = $connections;
        }
        return $this;
    }

    /**
     * Get static connections container
     * 
     * @return false|StaticEventCollection
     */
    public function getStaticConnections()
    {
        if (null === $this->staticConnections) {
            $this->setStaticConnections(StaticEventManager::getInstance());
        }
        return $this->staticConnections;
    }

    /**
     * Trigger all handlers for a given event
     * 
     * @param  string $event 
     * @param  string|object $context Object calling emit, or symbol describing context (such as static method name) 
     * @param  array|ArrayAccess $argv Array of arguments; typically, should be associative
     * @return ResponseCollection All handler return values
     */
    public function trigger($event, $context, $argv = array())
    {
        return $this->triggerUntil($event, $context, $argv, function(){
            return false;
        });
    }

    /**
     * Trigger handlers until return value of one causes a callback to 
     * evaluate to true
     *
     * Triggers handlers until the provided callback evaluates the return 
     * value of one as true, or until all handlers have been executed.
     * 
     * @param  string $event 
     * @param  string|object $context Object calling emit, or symbol describing context (such as static method name) 
     * @param  array|ArrayAccess $argv Array of arguments; typically, should be associative
     * @param  Callable $callback 
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function triggerUntil($event, $context, $argv, $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        $responses = new ResponseCollection;
        $e         = new $this->eventClass($event, $context, $argv);
        $handlers  = $this->getHandlers($event);

        if ($handlers->isEmpty()) {
            return $this->triggerStaticHandlers($callback, $e, $responses);
        }

        foreach ($handlers as $handler) {
            $responses->push(call_user_func($handler->getCallback(), $e));
            if ($e->propagationIsStopped()) {
                $responses->setStopped(true);
                break;
            }
            if (call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }

        if (!$responses->stopped()) {
            $this->triggerStaticHandlers($callback, $e, $responses);
        }
        return $responses;
    }

    /**
     * Attach a handler to an event
     *
     * The first argument is the event, and the next argument describes a 
     * callback that will respond to that event. A CallbackHandler instance 
     * describing the event handler combination will be returned.
     *
     * The last argument indicates a priority at which the event should be 
     * executed. By default, this value is 1; however, you may set it for any
     * integer value. Higher values have higher priority (i.e., execute first).
     * 
     * @param  string $event
     * @param  callback $callback PHP callback
     * @param  int $priority If provided, the priority at which to register the callback 
     * @return HandlerAggregate|CallbackHandler (to allow later unsubscribe)
     */
    public function attach($event, $callback, $priority = 1)
    {
        if (empty($this->events[$event])) {
            $this->events[$event] = new PriorityQueue();
        }
        $handler = new CallbackHandler($event, $callback, array('priority' => $priority));
        $this->events[$event]->insert($handler, $priority);
        return $handler;
    }

    /**
     * Attach a handler aggregate
     *
     * Handler aggregates accept an EventCollection instance, and call attach()
     * one or more times, typically to attach to multiple events using local 
     * methods.
     * 
     * @param  HandlerAggregate|string $aggregate 
     * @return HandlerAggregate
     */
    public function attachAggregate($aggregate)
    {
        if (is_string($aggregate)) {
            // Class name?
            if (!class_exists($aggregate)) {
                // Class doesn't exist; probably didn't provide a context
                throw new Exception\InvalidArgumentException(sprintf(
                    'No context provided for event "%s"',
                    $aggregate
                ));
            }
            // Create instance
            $aggregate = new $aggregate();
        }
        if (!$aggregate instanceof HandlerAggregate) {
            // Not an HandlerAggregate? We don't know how to handle it.
            throw new Exception\InvalidArgumentException(
                'Invalid class or object provided as event aggregate; must implement HandlerAggregate'
            );
        }

        // Have the event aggregate wire itself, and return it.
        $aggregate->attach($this);
        return $aggregate;
    }

    /**
     * Unsubscribe a handler from an event
     * 
     * @param  CallbackHandler $handler 
     * @return bool Returns true if event and handle found, and unsubscribed; returns false if either event or handle not found
     */
    public function detach(CallbackHandler $handler)
    {
        $event = $handler->getEvent();
        if (empty($this->events[$event])) {
            return false;
        }
        $return = $this->events[$event]->remove($handler);
        if (!$return) {
            return false;
        }
        if (!count($this->events[$event])) {
            unset($this->events[$event]);
        }
        return true;
    }

    /**
     * Detach a callback aggregate
     *
     * Loops through all handlers of all events to identify handlers that are
     * represented by the aggregate; for all matches, the handlers will be 
     * removed.
     * 
     * @param  HandlerAggregate $aggregate 
     * @return bool
     */
    public function detachAggregate(HandlerAggregate $aggregate)
    {
        foreach ($this->events as $event => $handlers) {
            foreach ($handlers as $key => $handler) {
                $callback = $handler->getCallback();
                if (is_object($callback)) {
                    if ($callback === $aggregate) {
                        $this->detach($handler);
                    }
                } elseif (is_array($callback)) {
                    if ($callback[0] === $aggregate) {
                        $this->detach($handler);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Retrieve all registered events
     * 
     * @return array
     */
    public function getEvents()
    {
        return array_keys($this->events);
    }

    /**
     * Retrieve all handlers for a given event
     * 
     * @param  string $event 
     * @return PriorityQueue
     */
    public function getHandlers($event)
    {
        if (!array_key_exists($event, $this->events)) {
            return new PriorityQueue();
        }
        return $this->events[$event];
    }

    /**
     * Clear all handlers for a given event
     * 
     * @param  string $event 
     * @return void
     */
    public function clearHandlers($event)
    {
        if (!empty($this->events[$event])) {
            unset($this->events[$event]);
        }
    }

    /**
     * Prepare arguments
     *
     * Use this method if you want to be able to modify arguments from within a
     * handler. It returns an ArrayObject of the arguments, which may then be 
     * passed to trigger() or triggerUntil().
     * 
     * @param  array $args 
     * @return ArrayObject
     */
    public function prepareArgs(array $args)
    {
        return new ArrayObject($args);
    }

    /**
     * Emit handlers matching the current identifier found in the static handler
     * 
     * @param  callback $callback 
     * @param  Event $event 
     * @param  ResponseCollection $responses 
     * @return ResponseCollection
     */
    protected function triggerStaticHandlers($callback, Event $event, ResponseCollection $responses)
    {
        if (!$staticConnections = $this->getStaticConnections()) {
            return $responses;
        }

        $identifiers = (array) $this->identifier;

        foreach ($identifiers as $id) {
            if (!$handlers = $staticConnections->getHandlers($id, $event->getName())) {
                continue;
            }
            foreach ($handlers as $handler) {
                $responses->push(call_user_func($handler->getCallback(), $event));
                if ($event->propagationIsStopped()) {
                    $responses->setStopped(true);
                    break;
                }
                if (call_user_func($callback, $responses->last())) {
                    $responses->setStopped(true);
                    break;
                }
            }
        }

        return $responses;
    }
}
