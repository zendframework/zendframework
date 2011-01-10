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

use Zend\Stdlib\CallbackHandler;

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
class Signals implements SignalSlot
{
    /**
     * Subscribed signals and their slots
     */
    protected $_signals = array();

    /**
     * Publish to all slots for a given signal
     * 
     * @param  string $signal 
     * @param  mixed $argv All arguments besides the signal are passed as arguments to the handler
     * @return ResponseCollection All handler return values
     */
    public function emit($signal, $argv = null)
    {
        if (!is_array($argv)) {
            $argv = func_get_args();
            $argv = array_slice($argv, 1);
        }
        return $this->emitUntil(function(){
            return false;
        }, $signal, $argv);
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
     * @param  mixed $argv All arguments besides the signal are passed as arguments to the handler
     * @return ResponseCollection All handler return values
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function emitUntil($callback, $signal, $argv = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        $responses = new ResponseCollection;

        if (empty($this->_signals[$signal])) {
            return $responses;
        }

        if (!is_array($argv)) {
            $argv   = func_get_args();
            $argv   = array_slice($argv, 2);
        }
        foreach ($this->_signals[$signal] as $slot) {
            $responses->push($slot->call($argv));
            if (call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
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
     * that the next arguments describe a callback that will respond to that
     * signal. A CallbackHandler instance describing the signal/handler 
     * combination will be returned.
     * 
     * @param  string|SignalAggregate $signalOrAggregate
     * @param  null|string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return SignalAggregate|CallbackHandler (to allow later unsubscribe)
     */
    public function connect($signalOrAggregate, $context = null, $handler = null)
    {
        if (null === $context) {
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

        if (empty($this->_signals[$signal])) {
            $this->_signals[$signal] = array();
        }
        $slot = new CallbackHandler($signal, $context, $handler);
        if ($index = array_search($slot, $this->_signals[$signal])) {
            return $this->_signals[$signal][$index];
        }
        $this->_signals[$signal][] = $slot;
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

        return $this->detachHandler($slot);
    }

    /**
     * Retrieve all registered signals
     * 
     * @return array
     */
    public function getSignals()
    {
        return array_keys($this->_signals);
    }

    /**
     * Retrieve all slots for a given signal
     * 
     * @param  string $signal 
     * @return \Zend\Stdlib\CallbackHandler[]
     */
    public function getHandlers($signal)
    {
        if (empty($this->_signals[$signal])) {
            return array();
        }
        return $this->_signals[$signal];
    }

    /**
     * Clear all slots for a given signal
     * 
     * @param  string $signal 
     * @return void
     */
    public function clearHandlers($signal)
    {
        if (!empty($this->_signals[$signal])) {
            unset($this->_signals[$signal]);
        }
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
        foreach ($this->_signals as $signal => $handlers) {
            foreach ($handlers as $key => $handler) {
                $callback = $handler->getCallback();
                if (is_object($callback)) {
                    if ($callback === $aggregate) {
                        $this->detachHandler($handler);
                    }
                } elseif (is_array($callback)) {
                    if ($callback[0] === $aggregate) {
                        $this->detachHandler($handler);
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
    protected function detachHandler(CallbackHandler $slot)
    {
        $signal = $slot->getSignal();
        if (empty($this->_signals[$signal])) {
            return false;
        }
        if (false === ($index = array_search($slot, $this->_signals[$signal]))) {
            return false;
        }
        unset($this->_signals[$signal][$index]);
        return true;
    }
}
