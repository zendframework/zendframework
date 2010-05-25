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
     * @var array All filters
     */
    protected $_filters = array();

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
        if (!is_array($argv)) {
            $argv = func_get_args();
            $argv = array_slice($argv, 1);
        }

        foreach ($this->_filters as $filter) {
            $callbackArgs = $argv;
            array_unshift($callbackArgs, $value);
            $value = $filter->call($callbackArgs);
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
    public function connect($context, $handler = null)
    {
        if (empty($context)) {
            throw new InvalidCallbackException('No callback provided');
        }
        $filter = new Slot(null, $context, $handler);
        if ($index = array_search($filter, $this->_filters)) {
            return $this->_filters[$index];
        }
        $this->_filters[] = $filter;
        return $filter;
    }

    /**
     * Unsubscribe a filter
     * 
     * @param  Slot $filter 
     * @return bool Returns true if filter found and unsubscribed; returns false otherwise
     */
    public function detach(Slot $filter)
    {
        if (false === ($index = array_search($filter, $this->_filters))) {
            return false;
        }
        unset($this->_filters[$index]);
        return true;
    }

    /**
     * Retrieve all filters
     * 
     * @return Slot[]
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Clear all filters
     * 
     * @return void
     */
    public function clearFilters()
    {
        $this->_filters = array();
    }
}
