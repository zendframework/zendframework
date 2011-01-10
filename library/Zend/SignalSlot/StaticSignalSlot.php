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

/**
 * Static version of Signals
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticSignalSlot implements StaticSignalManager
{
    /**
     * @var Signals
     */
    protected static $_instance;

    /**
     * Retrieve signals instance
     * 
     * @return SignalSlot
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::setInstance(new SignalSlot());
        }
        return self::$_instance;
    }

    /**
     * Set signal slot instance
     * 
     * @param  SignalManager|null $provider 
     * @return void
     */
    public static function setInstance(SignalManager $signals = null)
    {
        self::$_instance = $signals;
    }

    /**
     * Notify all slots for a given topic
     * 
     * @param  string $topic 
     * @param  mixed $args All arguments besides the topic are passed as arguments to the slot
     * @return ResponseCollection All handler return values 
     */
    public static function emit($signal, $args = null)
    {
        $signals = self::getInstance();
        $args    = func_get_args();
        $args    = array_slice($args, 1);
        return $signals->emit($signal, $args);
    }

    /**
     * Notify subscribers until return value of one causes a callback to 
     * evaluate to true
     *
     * Publishes subscribers until the provided callback evaluates the return 
     * value of one as true, or until all subscribers have been executed.
     * 
     * @param  Callable $callback 
     * @param  string $signal 
     * @param  mixed $argv All arguments besides the topic are passed as arguments to the slot
     * @return ResponseCollection All handler return values
     * @throws InvalidCallbackException if invalid callback provided
     */
    public static function emitUntil($callback, $signal, $args = null)
    {
        $signals = self::getInstance();
        $args    = func_get_args();
        $args    = array_slice($args, 2);
        return $signals->emitUntil($callback, $signal, $args);
    }

    /**
     * Attach a slot to a signal
     * 
     * @param  string|SignalAggregate $signal 
     * @param  null|callback $callback PHP Callback
     * @param  int $priority Priority at which slot should execute
     * @return CallbackHandler Pub-Sub handle (to allow later unsubscribe)
     */
    public static function connect($signalOrAggregate, $callback = null, $priority = 1)
    {
        $signals = self::getInstance();
        return $signals->connect($signalOrAggregate, $callback, $priority);
    }

    /**
     * Detach a slot from a signal 
     * 
     * @param  SignalAggregate|\Zend\Stdlib\CallbackHandler $handle 
     * @return bool Returns true if signal and slot found, and unsubscribed; returns false if either signal or slot not found
     */
    public static function detach($slot)
    {
        $signals = self::getInstance();
        return $signals->detach($slot);
    }

    /**
     * Retrieve all registered signals
     * 
     * @return array
     */
    public static function getSignals()
    {
        $signals = self::getInstance();
        return $signals->getSignals();
    }

    /**
     * Retrieve all slots for a given signal
     * 
     * @param  string $signal 
     * @return \Zend\Stdlib\SignalHandler[]
     */
    public static function getHandlers($signal)
    {
        $signals = self::getInstance();
        return $signals->getHandlers($signal);
    }

    /**
     * Clear all slots for a given signal
     * 
     * @param  string $signal 
     * @return void
     */
    public static function clearHandlers($signal)
    {
        $signals = self::getInstance();
        return $signals->clearHandlers($signal);
    }
}
