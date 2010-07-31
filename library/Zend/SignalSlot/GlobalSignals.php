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
 * Static version of Signals
 *
 * @uses       Zend\SignalSlot\StaticSignalSlot
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GlobalSignals implements StaticSignalSlot
{
    /**
     * @var Signals
     */
    protected static $_instance;

    /**
     * Retrieve signals instance
     * 
     * @return Signals
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::setInstance(new Signals());
        }
        return self::$_instance;
    }

    /**
     * Set Signals instance
     * 
     * @param  SignalSlot|null $provider 
     * @return void
     */
    public static function setInstance(SignalSlot $signals = null)
    {
        self::$_instance = $signals;
    }

    /**
     * Notify all slots for a given topic
     * 
     * @param  string $topic 
     * @param  mixed $args All arguments besides the topic are passed as arguments to the slot
     * @return void
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
     * @return mixed
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
     * @param  string $signal 
     * @param  string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return Handler Pub-Sub handle (to allow later unsubscribe)
     */
    public static function connect($signal, $context, $handler = null)
    {
        $signals = self::getInstance();
        return $signals->connect($signal, $context, $handler);
    }

    /**
     * Detach a slot from a signal 
     * 
     * @param  SignalHandler $handle 
     * @return bool Returns true if signal and slot found, and unsubscribed; returns false if either signal or slot not found
     */
    public static function detach(SignalHandler $slot)
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
     * @return SignalHandler[]
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
