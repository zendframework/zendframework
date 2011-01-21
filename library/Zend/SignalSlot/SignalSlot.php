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
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\SignalSlot;

use Zend\Stdlib\CallbackHandler,
    Zend\Stdlib\PriorityQueue,
    ArrayObject;

/**
 * Signals: notification system
 *
 * Use Signals when you want to create a per-instance plugin system for your 
 * objects.
 *
 * @uses       Zend\SignalSlot\SignalSlot
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SignalSlot implements SignalManager
{
    /**
     * Subscribed signals and their slots
     * @var array Array of PriorityQueue objects
     */
    protected $signals = array();

    /**
     * @var string Class representing the signal being emitted
     */
    protected $signalClass = 'Zend\SignalSlot\Signal';

    /**
     * Identifier, used to pull static signals from StaticSignalManager
     * @var null|string
     */
    protected $identifier;

    /**
     * Constructor
     *
     * Allows optionally specifying an identifier to use to pull signals from a 
     * StaticSignalManager.
     * 
     * @param  null|string|int $identifier 
     * @return void
     */
    public function __construct($identifier = null)
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the signal class to utilize
     * 
     * @param  string $class 
     * @return SignalSlot
     */
    public function setSignalClass($class)
    {
        $this->signalClass = $class;
        return $this;
    }

    /**
     * Publish to all slots for a given signal
     * 
     * @param  string $signal 
     * @param  string|object $context Object calling emit, or symbol describing context (such as static method name) 
     * @param  array $argv Array of arguments; typically, should be associative
     * @return ResponseCollection All handler return values
     */
    public function emit($signal, $context, $argv = array())
    {
        return $this->emitUntil($signal, $context, $argv, function(){
            return false;
        });
    }

    /**
     * Notify slots until return value of one causes a callback to 
     * evaluate to true
     *
     * Publishes slots until the provided callback evaluates the return 
     * value of one as true, or until all slots have been executed.
     * 
     * @param  Callable $callback 
     * @param  string $signal 
     * @param  string|object $context Object calling emit, or symbol describing context (such as static method name) 
     * @param  array $argv Array of arguments; typically, should be associative
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function emitUntil($signal, $context, $argv, $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        $responses = new ResponseCollection;
        $s         = new $this->signalClass($signal, $context, $argv);

        if (empty($this->signals[$signal])) {
            return $this->emitStaticSignals($callback, $s, $responses);
        }

        foreach ($this->signals[$signal] as $slot) {
            $responses->push(call_user_func($slot->getCallback(), $s));
            if ($s->propagationIsStopped()) {
                $responses->setStopped(true);
                break;
            }
            if (call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }
        if (!$responses->stopped()) {
            $this->emitStaticSignals($callback, $s, $responses);
        }
        return $responses;
    }

    /**
     * Subscribe to a signal
     *
     * If a SignalAggregate is provided as the first argument (as either a class
     * name or instance), this method will call the aggregate's connect() 
     * method, passing itself as the argument. Once done, the SignalAggregate
     * instance will be returned.
     *
     * Otherwise, the assumption is that the first argument is the signal, and 
     * that the next argument describes a callback that will respond to that
     * signal. A CallbackHandler instance describing the signal/handler 
     * combination will be returned.
     *
     * The last argument indicates a priority at which the signal should be 
     * executed. By default, this value is 1000; however, you may set it for any
     * integer value. Higher values have higher priority (i.e., execute first).
     * 
     * @param  string|SignalAggregate $signalOrAggregate
     * @param  null|callback $callback PHP callback
     * @param  null|int $priority If provided, the priority at which to register the callback 
     * @return SignalAggregate|CallbackHandler (to allow later unsubscribe)
     */
    public function connect($signalOrAggregate, $callback = null, $priority = 1000)
    {
        if (null === $callback) {
            // Assuming we have an aggregate that will self-register
            if (is_string($signalOrAggregate)) {
                // Class name?
                if (!class_exists($signalOrAggregate)) {
                    // Class doesn't exist; probably didn't provide a context
                    throw new Exception\InvalidArgumentException(sprintf(
                        'No context provided for signal "%s"',
                        $signalOrAggregate
                    ));
                }
                // Create instance
                $signalOrAggregate = new $signalOrAggregate();
            }
            if (!$signalOrAggregate instanceof SignalAggregate) {
                // Not a SignalAggregate? We don't know how to handle it.
                throw new Exception\InvalidArgumentException(
                    'Invalid class or object provided as signal aggregate; must implement SignalAggregate'
                );
            }

            // Have the signal aggregate wire itself, and return it.
            $signalOrAggregate->connect($this);
            return $signalOrAggregate;
        }

        // Handle normal signals
        $signal = $signalOrAggregate;

        if (empty($this->signals[$signal])) {
            $this->signals[$signal] = new PriorityQueue();
        }
        $slot = new CallbackHandler($signal, $callback, array('priority' => $priority));
        $this->signals[$signal]->insert($slot, $priority);
        return $slot;
    }

    /**
     * Unsubscribe a slot from a signal 
     * 
     * @param  SignalAggregate|CallbackHandler $slot 
     * @return bool Returns true if signal and handle found, and unsubscribed; returns false if either signal or handle not found
     */
    public function detach($slot)
    {
        if (!$slot instanceof SignalAggregate && !$slot instanceof CallbackHandler) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected CallbackHandler or SignalAggregate; received "%s"',
                (is_object($slot) ? get_class($slot) : gettype($slot))
            ));
        }

        if ($slot instanceof SignalAggregate) {
            return $this->detachAggregate($slot);
        }

        return $this->detachSlot($slot);
    }

    /**
     * Retrieve all registered signals
     * 
     * @return array
     */
    public function getSignals()
    {
        return array_keys($this->signals);
    }

    /**
     * Retrieve all slots for a given signal
     * 
     * @param  string $signal 
     * @return PriorityQueue
     */
    public function getSlots($signal)
    {
        if (!array_key_exists($signal, $this->signals)) {
            return new PriorityQueue();
        }
        return $this->signals[$signal];
    }

    /**
     * Clear all slots for a given signal
     * 
     * @param  string $signal 
     * @return void
     */
    public function clearSlots($signal)
    {
        if (!empty($this->signals[$signal])) {
            unset($this->signals[$signal]);
        }
    }

    /**
     * Prepare arguments
     *
     * Use this method if you want to be able to modify arguments from within a
     * slot. It returns an ArrayObject of the arguments, which may then be 
     * passed to emit() or emitUntil().
     * 
     * @param  array $args 
     * @return ArrayObject
     */
    public function prepareArgs(array $args)
    {
        return new ArrayObject($args);
    }

    /**
     * Detach a SignalAggregate
     *
     * Iterates through all signals, testing handlers to determine if they
     * represent the provided SignalAggregate; if so, they are then removed.
     * 
     * @param  SignalAggregate $aggregate 
     * @return true
     */
    protected function detachAggregate(SignalAggregate $aggregate)
    {
        foreach ($this->signals as $signal => $slots) {
            foreach ($slots as $key => $slot) {
                $callback = $slot->getCallback();
                if (is_object($callback)) {
                    if ($callback === $aggregate) {
                        $this->detachSlot($slot);
                    }
                } elseif (is_array($callback)) {
                    if ($callback[0] === $aggregate) {
                        $this->detachSlot($slot);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Detach a signal handler
     * 
     * @param  CallbackHandler $slot 
     * @return bool
     */
    protected function detachSlot(CallbackHandler $slot)
    {
        $signal = $slot->getSignal();
        if (empty($this->signals[$signal])) {
            return false;
        }
        return $this->signals[$signal]->remove($slot);
    }

    /**
     * Emit signals matching the current identifier found in the static handler
     * 
     * @param  callback $callback 
     * @param  Signal $signal 
     * @param  ResponseCollection $responses 
     * @return ResponseCollection
     */
    protected function emitStaticSignals($callback, Signal $signal, ResponseCollection $responses)
    {
        if (!$slots = StaticSignalSlot::getInstance()->getSlots($this->identifier, $signal->getName())) {
            return $responses;
        }
        foreach ($slots as $slot) {
            $responses->push(call_user_func($slot->getCallback(), $signal));
            if ($signal->propagationIsStopped()) {
                $responses->setStopped(true);
                break;
            }
            if (call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }
        return $responses;
    }
}
