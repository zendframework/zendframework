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
     * @param  string|array|object $callback PHP callback (first element may be )
     * @param  array $options Options used by the callback handler (e.g., priority)
     * @return void
     */
    public function __construct($event, $callback, array $options = array())
    {
        $this->event    = $event;
        $this->options  = $options;
        $this->registerCallback($callback);
    }

    protected function registerCallback($callback)
    {
        if (is_object($callback) && !$callback instanceof Closure) {
            if (class_exists('WeakRef', false)) {
                $this->callback = new WeakRef($callback);
            }
            return;
        }

        if (!is_array($callback)) {
            $this->callback = $callback;
            return;
        }

        if (!class_exists('WeakRef', false)) {
            $this->callback = $callback;
            return;
        }

        list($target, $method) = $callback;
        if (!is_object($target)) {
            $this->callback = $callback;
            return;
        }

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
     * @throws Exception\InvalidCallbackException
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

    public function isValid()
    {
        if ($this->isValidCallback) {
            return $this->callback;
        }

        $callback = $this->callback;

        if (is_string($callback)) {
            return $this->validateStringCallback($callback);
        }

        if ($callback instanceof \WeakRef) {
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
        if ($target instanceof \WeakRef) {
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
     * @return Callback
     * @throws Exception\InvalidCallbackException
     */
    protected function validateStringCallback($callback)
    {
        if (is_callable($callback)) {
            $this->isValidCallback = true;
            return $callback;
        }

        if (!class_exists($callback)) {
            // throw new Exception\InvalidCallbackException('Provided callback is not a function or a class');
            return false;
        }

        // check __invoke before instantiating
        if (!method_exists($callback, '__invoke')) {
            // throw new Exception\InvalidCallbackException('Class provided as a callback does not implement __invoke');
            return false;
        }
        $object = new $callback();

        $this->callback        = $object;
        $this->isValidCallback = true;
        return $object;
    }

    /**
     * Validate an array callback
     * 
     * @param  array $callback 
     * @return callback
     * @throws Exception\InvalidCallbackException
     */
    protected function validateArrayCallback(array $callback)
    {
        $context = $callback[0];
        $method  = $callback[1];

        if (is_string($context)) {
            // Dealing with a class/method callback, and class provided is a string classname
            
            if (!class_exists($context)) {
                // throw new Exception\InvalidCallbackException('Class provided in callback does not exist');
                return false;
            }

            // We need to determine if we need to instantiate the class first
            $r = new \ReflectionClass($context);
            if (!$r->hasMethod($method)) {
                // Explicit method does not exist
                if (!$r->hasMethod('__callStatic') && !$r->hasMethod('__call')) {
                    // throw new Exception\InvalidCallbackException('Class provided in callback does not define the method requested');
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

        // throw new Exception\InvalidCallbackException('Method provided in callback does not exist in object');
        return false;
    }
}
