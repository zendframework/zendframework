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
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib;

use Closure,
    WeakRef;

/**
 * CallbackHandler
 *
 * A handler for a event, event, filterchain, etc. Abstracts PHP callbacks,
 * primarily to allow for lazy-loading and ensuring availability of default
 * arguments (currying).
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CallbackHandler
{
    /**
     * @var string|array PHP callback to invoke
     */
    protected $callback;

    /**
     * @var string Event to which this handle is subscribed
     */
    protected $event;

    /**
     * Until callback has been validated, mark as invalid
     * @var bool
     */
    protected $isValidCallback = false;

    /**
     * Callback options, if any
     * @var array
     */
    protected $options;

    /**
     * Constructor
     * 
     * @param  string $event Event to which slot is subscribed
     * @param  string|array|object $callback PHP callback 
     * @param  array $options Options used by the callback handler (e.g., priority)
     * @return void
     */
    public function __construct($event, $callback, array $options = array())
    {
        $this->event    = $event;
        $this->options  = $options;
        $this->registerCallback($callback);
    }

    /**
     * Registers the callback provided in the constructor
     *
     * If you have pecl/weakref {@see http://pecl.php.net/weakref} installed, 
     * this method provides additional behavior.
     *
     * If a callback is a functor, or an array callback composing an object 
     * instance, this method will pass the object to a WeakRef instance prior
     * to registering the callback. See {@link isValid()} for more information
     * on how this affects execution.
     * 
     * @param  callback $callback 
     * @return void
     */
    protected function registerCallback($callback)
    {
        // If pecl/weakref is not installed, simply register it
        if (!class_exists('WeakRef', false)) {
            $this->callback = $callback;
            return;
        }

        // If we have a non-closure object, pass it to WeakRef, and then
        // register it.
        if (is_object($callback) && !$callback instanceof Closure) {
            $this->callback = new WeakRef($callback);
            return;
        }

        // If we have a string or closure, register as-is
        if (!is_array($callback)) {
            $this->callback = $callback;
            return;
        }

        list($target, $method) = $callback;

        // If we have an array callback, and the first argument is not an 
        // object, register as-is
        if (!is_object($target)) {
            $this->callback = $callback;
            return;
        }

        // We have an array callback with an object as the first argument;
        // pass it to WeakRef, and then register the new callback
        $target = new WeakRef($target);
        $this->callback = array($target, $method);
    }

    /**
     * Get event to which handler is subscribed
     * 
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Retrieve registered callback
     * 
     * @return Callback
     * @throws Exception\InvalidCallbackException If callback is invalid
     */
    public function getCallback()
    {
        if (!$this->isValid()) {
            throw new Exception\InvalidCallbackException('Invalid callback provided; not callable');
        }

        $callback = $this->callback;
        if (is_string($callback)) {
            return $callback;
        }

        if ($callback instanceof WeakRef) {
            return $callback->get();
        }

        if (is_object($callback)) {
            return $callback;
        }

        list($target, $method) = $callback;
        if ($target instanceof WeakRef) {
            return array($target->get(), $method);
        }

        return $callback;
    }

    /**
     * Invoke handler
     * 
     * @param  array $args Arguments to pass to callback
     * @return mixed
     */
    public function call(array $args = array())
    {
        $callback = $this->getCallback();
        return call_user_func_array($callback, $args);
    }

    /**
     * Get all callback options
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Retrieve a single option
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        return null;
    }

    /**
     * Is the composed callback valid?
     *
     * Typically, this method simply checks to see if we have a valid callback. 
     * In a few situations, it does more.
     *
     * * If we have a string callback, we pass execution to 
     *   {@link validateStringCallback()}.
     * * If we have an object callback, we test to see if that object is a 
     *   WeakRef {@see http://pecl.php.net/weakref}. If so, we return the value
     *   of its valid() method. Otherwise, we return the result of is_callable().
     * * If we have a callback array with a string in the first position, we 
     *   pass execution to {@link validateArrayCallback()}.
     * * If we have a callback array with an object in the first position, we 
     *   test to see if that object is a WeakRef (@see http://pecl.php.net/weakref).
     *   If so, we return the value of its valid() method. Otherwise, we return 
     *   the result of is_callable() on the callback.
     *
     * WeakRef is used to allow listeners to go out of scope. This functionality
     * is turn-key if you have pecl/weakref installed; otherwise, you will have
     * to manually remove listeners before destroying an object referenced in a
     * listener.
     *
     * @return bool
     */
    public function isValid()
    {
        // If we've already tested this, we can move on. Note: if a callback
        // composes a WeakRef, this will never get set, and thus result in
        // validation on each call.
        if ($this->isValidCallback) {
            return $this->callback;
        }

        $callback = $this->callback;

        if (is_string($callback)) {
            return $this->validateStringCallback($callback);
        }

        if ($callback instanceof WeakRef) {
            return $callback->valid();
        }

        if (is_object($callback) && is_callable($callback)) {
            $this->isValidCallback = true;
            return true;
        }

        if (!is_array($callback)) {
            return false;
        }

        list($target, $method) = $callback;
        if ($target instanceof WeakRef) {
            if (!$target->valid()) {
                return false;
            }
            $target = $target->get();
            return is_callable(array($target, $method));
        }
        return $this->validateArrayCallback($callback);
    }

    /**
     * Validate a string callback
     *
     * Check first if the string provided is callable. If not see if it is a 
     * valid class name; if so, determine if the object is invokable.
     * 
     * @param  string $callback 
     * @return bool
     */
    protected function validateStringCallback($callback)
    {
        if (is_callable($callback)) {
            $this->isValidCallback = true;
            return true;
        }

        if (!class_exists($callback)) {
            return false;
        }

        // check __invoke before instantiating
        if (!method_exists($callback, '__invoke')) {
            return false;
        }
        $object = new $callback();

        $this->callback        = $object;
        $this->isValidCallback = true;
        return true;
    }

    /**
     * Validate an array callback
     * 
     * @param  array $callback 
     * @return bool
     */
    protected function validateArrayCallback(array $callback)
    {
        $context = $callback[0];
        $method  = $callback[1];

        if (is_string($context)) {
            // Dealing with a class/method callback, and class provided is a string classname
            
            if (!class_exists($context)) {
                return false;
            }

            // We need to determine if we need to instantiate the class first
            $r = new \ReflectionClass($context);
            if (!$r->hasMethod($method)) {
                // Explicit method does not exist
                if (!$r->hasMethod('__callStatic') && !$r->hasMethod('__call')) {
                    return false;
                }

                if ($r->hasMethod('__callStatic')) {
                    // We have a __callStatic defined, so the original callback is valid
                    $this->isValidCallback = true;
                    return $callback;
                }

                // We have __call defined, so we need to instantiate the class 
                // first, and redefine the callback
                $object                 = new $context();
                $this->callback        = array($object, $method);
                $this->isValidCallback = true;
                return $this->callback;
            }

            // Explicit method exists
            $rMethod = $r->getMethod($method);
            if ($rMethod->isStatic()) {
                // Method is static, so original callback is fine
                $this->isValidCallback = true;
                return $callback;
            }

            // Method is an instance method; instantiate object and redefine callback
            $object                 = new $context();
            $this->callback        = array($object, $method);
            $this->isValidCallback = true;
            return $this->callback;
        } elseif (is_callable($callback)) {
            // The 
            $this->isValidCallback = true;
            return $callback;
        }

        return false;
    }
}
