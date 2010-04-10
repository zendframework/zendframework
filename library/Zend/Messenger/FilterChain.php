<?php
/**
 * Phly - PHp LibrarY
 * 
 * @package   Zend
 * @category  Zend
 * @package   Zend_Messenger
 */

namespace Zend\Messenger;

/**
 * FilterChain: subject/observer filter chain system
 */
class FilterChain
{
    /**
     * @var array All subscribers
     */
    protected $_handlers = array();

    /**
     * Publish to all subscribers
     *
     * All arguments are passed to each subscriber
     * 
     * @param  mixed $argv Arguments to pass to subscribers (optional)
     * @return void
     */
    public function notify($argv = null)
    {
        $return = null;
        $argv   = func_get_args();
        foreach ($this->_handlers as $handle) {
            $return = $handle->call($argv);
        }
        return $return;
    }

    /**
     * Notify subscribers until return value of one causes a callback to 
     * evaluate to true
     *
     * Publishes subscribers until the provided callback evaluates the return 
     * value of one as true, or until all subscribers have been executed.
     * 
     * @param  Callable $callback 
     * @param  mixed $argv All arguments are passed to subscribers (optional)
     * @return mixed
     * @throws InvalidCallbackException if invalid callback provided
     */
    public function notifyUntil($callback, $argv = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('Invalid filter callback provided');
        }

        $return = null;
        $argv   = func_get_args();
        array_shift($argv);

        foreach ($this->_handlers as $handle) {
            $return = $handle->call($argv);
            if (call_user_func($callback, $return)) {
                break;
            }
        }
        return $return;
    }

    /**
     * Filter a value
     *
     * Notifies all subscribers passes the single value provided
     * as an argument. Each subsequent subscriber is passed the return value
     * of the previous subscriber, and the value of the last subscriber is 
     * returned.
     * 
     * @param  mixed $value Value to filter
     * @param  mixed $argv Any additional arguments
     * @return mixed
     */
    public function filter($value, $argv = null)
    {
        $argv = func_get_args();
        array_shift($argv);

        foreach ($this->_handlers as $handle) {
            $callbackArgs = $argv;
            array_unshift($callbackArgs, $value);
            $value = $handle->call($callbackArgs);
        }
        return $value;
    }

    /**
     * Subscribe
     * 
     * @param  string|object $context Function name, class name, or object instance
     * @param  null|string $handler If $context is a class or object, the name of the method to call
     * @return Handler Pub-Sub handle (to allow later unsubscribe)
     */
    public function attach($context, $handler = null)
    {
        if (empty($context)) {
            throw new InvalidCallbackException('No callback provided');
        }
        $handle = new Handler(null, $context, $handler);
        if ($index = array_search($handle, $this->_handlers)) {
            return $this->_handlers[$index];
        }
        $this->_handlers[] = $handle;
        return $handle;
    }

    /**
     * Unsubscribe a handler
     * 
     * @param  Handler $handle 
     * @return bool Returns true if topic and handle found, and unsubscribed; returns false if handle not found
     */
    public function detach(Handler $handle)
    {
        if (false === ($index = array_search($handle, $this->_handlers))) {
            return false;
        }
        unset($this->_handlers[$index]);
        return true;
    }

    /**
     * Retrieve all handlers
     * 
     * @param  string $topic 
     * @return array Array of Handler objects
     */
    public function getHandlers()
    {
        return $this->_handlers;
    }

    /**
     * Clear all handlers
     * 
     * @return void
     */
    public function clearHandlers()
    {
        $this->_handlers = array();
    }
}
