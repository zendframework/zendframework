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
 * @package    Zend_Messenger
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Messenger;

/**
 * FilterChain: subject/observer filter chain system
 *
 * @uses       Zend\Messenger\Filter
 * @category   Zend
 * @package    Zend_Messenger
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterChain implements Filter
{
    /**
     * @var array All subscribers
     */
    protected $_handlers = array();

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
