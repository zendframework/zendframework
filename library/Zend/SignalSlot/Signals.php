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

use Zend\Stdlib\SignalHandler;

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
     * @return void
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
     * @return mixed
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function emitUntil($callback, $signal, $argv = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid callback provided');
        }

        if (empty($this->_signals[$signal])) {
            return;
        }

        $return = null;
        if (!is_array($argv)) {
            $argv   = func_get_args();
            $argv   = array_slice($argv, 2);
        }
        foreach ($this->_signals[$signal] as $slot) {
            $return = $slot->call($argv);
            if (call_user_func($callback, $return)) {
                break;
            }
        }
        return $return;
    }

    /**
     * Subscribe to a signal
     * 
     * @param  string $signal 
     * @param  string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return SignalHandler (to allow later unsubscribe)
     */
    public function connect($signal, $context, $handler = null)
    {
        if (empty($this->_signals[$signal])) {
            $this->_signals[$signal] = array();
        }
        $slot = new SignalHandler($signal, $context, $handler);
        if ($index = array_search($slot, $this->_signals[$signal])) {
            return $this->_signals[$signal][$index];
        }
        $this->_signals[$signal][] = $slot;
        return $slot;
    }

    /**
     * Unsubscribe a slot from a signal 
     * 
     * @param  SignalHandler $slot 
     * @return bool Returns true if signal and handle found, and unsubscribed; returns false if either signal or handle not found
     */
    public function detach(SignalHandler $slot)
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
     * @return SignalHandler[]
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
}
