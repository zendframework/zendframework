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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib;

use Closure,
    ReflectionClass,
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CallbackHandler
{
    /**
     * @var string|array PHP callback to invoke
     */
    protected $callback;

    /**
     * Until callback has been validated, mark as invalid
     * @var bool
     */
    protected $isValidCallback = false;

    /**
     * Callback metadata, if any
     * @var array
     */
    protected $metadata;

    /**
     * Constructor
     * 
     * @param  string $event Event to which slot is subscribed
     * @param  string|array|object $callback PHP callback 
     * @param  array $options Options used by the callback handler (e.g., priority)
     * @return void
     */
    public function __construct($callback, array $metadata = array())
    {
        $this->metadata  = $metadata;
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
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('Invalid callback provided; not callable');
        }

        // If pecl/weakref is not installed, simply store the callback and return
        if (!class_exists('WeakRef')) {
            $this->validateCallback($callback);
            $this->callback = $callback;
            return;
        }

        // If WeakRef exists, we want to use it.

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
            $this->validateCallback($callback);
            $this->callback = $callback;
            return;
        }

        // We have an array callback with an object as the first argument;
        // pass it to WeakRef, and then register the new callback
        $target = new WeakRef($target);
        $this->callback = array($target, $method);
    }

    /**
     * Retrieve registered callback
     * 
     * @return Callback
     * @throws Exception\InvalidCallbackException If callback is invalid
     */
    public function getCallback()
    {
        $callback = $this->callback;

        // String callbacks -- simply return
        if (is_string($callback)) {
            return $callback;
        }

        // WeakRef callbacks -- pull it out of the object and return it
        if ($callback instanceof WeakRef) {
            return $callback->get();
        }

        // Non-WeakRef object callback -- return it
        if (is_object($callback)) {
            return $callback;
        }

        // Array callback with WeakRef object -- retrieve the object first, and 
        // then return
        list($target, $method) = $callback;
        if ($target instanceof WeakRef) {
            return array($target->get(), $method);
        }

        // Otherwise, return it
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

        $isPhp54 = version_compare(PHP_VERSION, '5.4.0rc1', '>=');

        // Minor performance tweak; use call_user_func() until > 3 arguments 
        // reached
        switch (count($args)) {
            case 0:
                if ($isPhp54) {
                    return $callback();
                }
                return call_user_func($callback);
            case 1:
                if ($isPhp54) {
                    return $callback(array_shift($args));
                }
                return call_user_func($callback, array_shift($args));
            case 2:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);
                if ($isPhp54) {
                    return $callback($arg1, $arg2);
                }
                return call_user_func($callback, $arg1, $arg2);
            case 3:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);
                $arg3 = array_shift($args);
                if ($isPhp54) {
                    return $callback($arg1, $arg2, $arg3);
                }
                return call_user_func($callback, $arg1, $arg2, $arg3);
            default:
                return call_user_func_array($callback, $args);
        }
    }

    /**
     * Invoke as functor
     * 
     * @return mixed
     */
    public function __invoke()
    {
        return $this->call(func_get_args());
    }

    /**
     * Get all callback metadata
     * 
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Retrieve a single metadatum
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getMetadatum($name)
    {
        if (array_key_exists($name, $this->metadata)) {
            return $this->metadata[$name];
        }
        return null;
    }

    /**
     * Validate a callback
     *
     * Attempts to ensure that the provided callback is actually callable.
     *
     * Interestingly, given a class "Foo" with a non-static method "bar", 
     * is_callable() still returns true for the following callbacks:
     *
     * - array('Foo', 'bar')
     * - 'Foo::bar'
     *
     * even though passing these to call_user_func*() will raise an error.
     * 
     * @param  string|array|object $callback 
     * @return bool
     * @throws Exception\InvalidCallbackException
     */
    protected function validateCallback($callback)
    {
        if (is_array($callback)) {
            return $this->validateArrayCallback($callback);
        }
        if (is_string($callback)) {
            return $this->validateStringCallback($callback);
        }
        return true;
    }

    /**
     * Validate an array callback
     *
     * If the first argument of the array is an object, simply returns true. 
     * Otherwise, passes the array arguments off to validateStaticMethod().
     *
     * @param  array $callback 
     * @return true
     * @throws Exception\InvalidCallbackException
     */
    protected function validateArrayCallback($callback)
    {
        list($target, $method) = $callback;
        if (is_object($target)) {
            return true;
        }

        return $this->validateStaticMethod($target, $method);
    }

    /**
     * Validate a string callback
     *
     * If the callback is a function name, simply returns true. If it refers
     * to a static method, proxies to validateStaticMethod().
     * 
     * @param  string $callback 
     * @return true
     * @throws Exception\InvalidCallbackException
     */
    protected function validateStringCallback($callback)
    {
        if (!strstr($callback, '::')) {
            return true;
        }

        list($target, $method) = explode('::', $callback, 2);
        return $this->validateStaticMethod($target, $method);
    }

    /**
     * Validates that a static callback actually exists
     * 
     * @param  string $target 
     * @param  string $method 
     * @return bool
     * @throws Exception\InvalidCallbackException
     */
    protected function validateStaticMethod($target, $method)
    {
        $r = new ReflectionClass($target);
        if (!$r->hasMethod($method)) {
            throw new Exception\InvalidCallbackException('Invalid callback; method does not exist');
        }

        $m = $r->getMethod($method);
        if (!$m->isStatic()) {
            throw new Exception\InvalidCallbackException('Invalid callback; method is not static');
        }

        return true;
    }
}
