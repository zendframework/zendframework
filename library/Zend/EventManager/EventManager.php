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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\EventManager;

use Zend\Stdlib\CallbackHandler,
    Zend\Stdlib\Exception\InvalidCallbackException,
    Zend\Stdlib\PriorityQueue,
    ArrayObject,
    SplPriorityQueue,
    Traversable;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification
 * system for your objects.
 *
 * @category   Zend
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EventManager implements EventCollection
{
    /**
     * Subscribed events and their listeners
     * @var array Array of PriorityQueue objects
     */
    protected $events = array();

    /**
     * @var string Class representing the event being emitted
     */
    protected $eventClass = 'Zend\EventManager\Event';

    /**
     * Identifiers, used to pull static signals from StaticEventManager
     * @var array
     */
    protected $identifiers = array();

    /**
     * Static connections
     * @var false|null|StaticEventCollection
     */
    protected $staticConnections = null;

    /**
     * Constructor
     *
     * Allows optionally specifying identifier(s) to use to pull signals from a
     * StaticEventManager.
     *
     * @param  null|string|int|array|Traversable $identifiers
     * @return void
     */
    public function __construct($identifiers = null)
    {
        $this->setIdentifiers($identifiers);
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
     * Get the identifier(s) for this EventManager
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * Set the identifiers (overrides any currently set identifiers)
     *
     * @param string|int|array|Traversable $identifiers
     * @return ModuleManager
     */
    public function setIdentifiers($identifiers)
    {
        if (is_array($identifiers) || $identifiers instanceof \Traversable) {
            $this->identifiers = array_unique((array) $identifiers);
        } elseif ($identifiers !== null) {
            $this->identifiers = array($identifiers);
        }
        return $this;
    }

    /**
     * Add some identifier(s) (appends to any currently set identifiers)
     *
     * @param string|int|array|Traversable $identifiers
     * @return ModuleManager
     */
    public function addIdentifiers($identifiers)
    {
        if (is_array($identifiers) || $identifiers instanceof \Traversable) {
            $this->identifiers = array_unique($this->identifiers + (array) $identifiers);
        } elseif ($identifiers !== null) {
            $this->identifiers = array_unique($this->identifiers + array($identifiers));
        }
        return $this;
    }

    /**
     * Trigger all listeners for a given event
     *
     * Can emulate triggerUntil() if the last argument provided is a callback.
     *
     * @param  string $event
     * @param  string|object $target Object calling emit, or symbol describing target (such as static method name)
     * @param  array|ArrayAccess $argv Array of arguments; typically, should be associative
     * @param  null|callback $callback
     * @return ResponseCollection All listener return values
     */
    public function trigger($event, $target = null, $argv = array(), $callback = null)
    {
        if ($event instanceof EventDescription) {
            $e        = $event;
            $event    = $e->getName();
            $callback = $target;
        } elseif ($target instanceof EventDescription) {
            $e = $target;
            $e->setName($event);
            $callback = $argv;
        } elseif ($argv instanceof EventDescription) {
            $e = $argv;
            $e->setName($event);
            $e->setTarget($target);
        } else {
            $e = new $this->eventClass();
            $e->setName($event);
            $e->setTarget($target);
            $e->setParams($argv);
        }

        if ($callback && !is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        return $this->triggerListeners($event, $e, $callback);
    }

    /**
     * Trigger listeners until return value of one causes a callback to
     * evaluate to true
     *
     * Triggers listeners until the provided callback evaluates the return
     * value of one as true, or until all listeners have been executed.
     *
     * @param  string $event
     * @param  string|object $target Object calling emit, or symbol describing target (such as static method name)
     * @param  array|ArrayAccess $argv Array of arguments; typically, should be associative
     * @param  Callable $callback
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function triggerUntil($event, $target, $argv = null, $callback = null)
    {
        if ($event instanceof EventDescription) {
            $e        = $event;
            $event    = $e->getName();
            $callback = $target;
        } elseif ($target instanceof EventDescription) {
            $e = $target;
            $e->setName($event);
            $callback = $argv;
        } elseif ($argv instanceof EventDescription) {
            $e = $argv;
            $e->setName($event);
            $e->setTarget($target);
        } else {
            $e = new $this->eventClass();
            $e->setName($event);
            $e->setTarget($target);
            $e->setParams($argv);
        }

        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        return $this->triggerListeners($event, $e, $callback);
    }

    /**
     * Attach a listener to an event
     *
     * The first argument is the event, and the next argument describes a
     * callback that will respond to that event. A CallbackHandler instance
     * describing the event listener combination will be returned.
     *
     * The last argument indicates a priority at which the event should be
     * executed. By default, this value is 1; however, you may set it for any
     * integer value. Higher values have higher priority (i.e., execute first).
     *
     * @param  string|ListenerAggregate $event
     * @param  callback|int $callback If string $event provided, expects PHP callback; for a ListenerAggregate $event, this will be the priority
     * @param  int $priority If provided, the priority at which to register the callback
     * @return CallbackHandler|mixed CallbackHandler if attaching callback (to allow later unsubscribe); mixed if attaching aggregate
     */
    public function attach($event, $callback = null, $priority = 1)
    {
        if ($event instanceof ListenerAggregate) {
            return $this->attachAggregate($event, $callback);
        }

        if (null === $callback) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects a callback; none provided',
                __METHOD__
            ));
        }

        if (empty($this->events[$event])) {
            $this->events[$event] = new PriorityQueue();
        }
        $listener = new CallbackHandler($callback, array('event' => $event, 'priority' => $priority));
        $this->events[$event]->insert($listener, $priority);
        return $listener;
    }

    /**
     * Attach a listener aggregate
     *
     * Listener aggregates accept an EventCollection instance, and call attach()
     * one or more times, typically to attach to multiple events using local
     * methods.
     *
     * @param  ListenerAggregate $aggregate
     * @param  int $priority If provided, a suggested priority for the aggregate to use
     * @return mixed return value of {@link ListenerAggregate::attach()}
     */
    public function attachAggregate(ListenerAggregate $aggregate, $priority = 1)
    {
        return $aggregate->attach($this, $priority);
    }

    /**
     * Unsubscribe a listener from an event
     *
     * @param  CallbackHandler|ListenerAggregate $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     * @throws Exception\InvalidArgumentException if invalid listener provided
     */
    public function detach($listener)
    {
        if ($listener instanceof ListenerAggregate) {
            return $this->detachAggregate($listener);
        }

        if (!$listener instanceof CallbackHandler) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expected a ListenerAggregate or CallbackHandler; received "%s"',
                __METHOD__,
                (is_object($listener) ? get_class($listener) : gettype($listener))
            ));
        }

        $event = $listener->getMetadatum('event');
        if (!$event || empty($this->events[$event])) {
            return false;
        }
        $return = $this->events[$event]->remove($listener);
        if (!$return) {
            return false;
        }
        if (!count($this->events[$event])) {
            unset($this->events[$event]);
        }
        return true;
    }

    /**
     * Detach a listener aggregate
     *
     * Listener aggregates accept an EventCollection instance, and call detach()
     * of all previously attached listeners.
     *
     * @param  ListenerAggregate $aggregate
     * @return mixed return value of {@link ListenerAggregate::detach()}
     */
    public function detachAggregate(ListenerAggregate $aggregate)
    {
        return $aggregate->detach($this);
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
     * Retrieve all listeners for a given event
     *
     * @param  string $event
     * @return PriorityQueue
     */
    public function getListeners($event)
    {
        if (!array_key_exists($event, $this->events)) {
            return new PriorityQueue();
        }
        return $this->events[$event];
    }

    /**
     * Clear all listeners for a given event
     *
     * @param  string $event
     * @return void
     */
    public function clearListeners($event)
    {
        if (!empty($this->events[$event])) {
            unset($this->events[$event]);
        }
    }

    /**
     * Prepare arguments
     *
     * Use this method if you want to be able to modify arguments from within a
     * listener. It returns an ArrayObject of the arguments, which may then be
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
     * Trigger listeners
     *
     * Actual functionality for triggering listeners, to which both trigger() and triggerUntil()
     * delegate.
     *
     * @param  string           $event Event name
     * @param  EventDescription $e
     * @param  null|callback    $callback
     * @return ResponseCollection
     */
    protected function triggerListeners($event, EventDescription $e, $callback = null)
    {
        $responses = new ResponseCollection;
        $listeners = $this->getListeners($event);

        // add static listeners to the list of listeners
        // but don't modify the listeners object
        $staticListeners = $this->getStaticListeners($event);
        if (count($staticListeners)) {
            $listeners = clone $listeners;
            foreach ($staticListeners as $listener) {
                $priority = $listener->getMetadatum('priority');
                if (null === $priority) {
                    $priority = 1;
                } elseif (is_array($priority)) {
                    // If we have an array, likely using PriorityQueue. Grab first
                    // element of the array, as that's the actual priority.
                    $priority = array_shift($priority);
                }
                $listeners->insert($listener, $priority);
            }
        }

        if ($listeners->isEmpty()) {
            return $responses;
        }

        foreach ($listeners as $listener) {
            // Trigger the listener's callback, and push its result onto the
            // response collection
            $responses->push(call_user_func($listener->getCallback(), $e));

            // If the event was asked to stop propagating, do so
            if ($e->propagationIsStopped()) {
                $responses->setStopped(true);
                break;
            }

            // If the result causes our validation callback to return true,
            // stop propagation
            if ($callback && call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }

        return $responses;
    }

    /**
     * Get list of all listeners attached to the static collection for
     * identifiers registered by this instance
     *
     * @param  string $event
     * @return array
     */
    protected function getStaticListeners($event)
    {
        if (!$staticConnections = $this->getStaticConnections()) {
            return array();
        }

        $identifiers     = $this->getIdentifiers();
        $staticListeners = array();

        foreach ($identifiers as $id) {
            if (!$listeners = $staticConnections->getListeners($id, $event)) {
                continue;
            }

            if (!is_array($listeners) && !($listeners instanceof Traversable)) {
                continue;
            }

            foreach ($listeners as $listener) {
                if (!$listener instanceof CallbackHandler) {
                    continue;
                }
                $staticListeners[] = $listener;
            }
        }

        return $staticListeners;
    }
}
