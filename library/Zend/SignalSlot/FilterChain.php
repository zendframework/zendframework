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
 * @copyright  Copyright (c) 2010-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\SignalSlot;

use Zend\Stdlib\CallbackHandler,
    Zend\Stdlib\PriorityQueue,
    Zend\Stdlib\Exception\InvalidCallbackException;

/**
 * FilterChain: subject/observer filter chain system
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterChain implements Filter
{
    /**
     * @var PriorityQueue All filters
     */
    protected $filters;

    /**
     * Constructor
     *
     * Initialize priority queue used to store filters.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->filters = new PriorityQueue();
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
        if (!is_array($argv)) {
            $argv = func_get_args();
            $argv = array_slice($argv, 1);
        }

        foreach ($this->filters as $filter) {
            $callbackArgs = $argv;
            array_unshift($callbackArgs, $value);
            $value = $filter->call($callbackArgs);
        }
        return $value;
    }

    /**
     * Subscribe
     * 
     * @param  callback $callback PHP Callback
     * @param  int $priority Priority in the queue at which to execute
     * @return CallbackHandler Pub-Sub handle (to allow later unsubscribe)
     */
    public function connect($callback, $priority = 1)
    {
        if (empty($callback)) {
            throw new InvalidCallbackException('No callback provided');
        }
        $filter = new CallbackHandler(null, $callback, array('priority' => $priority));
        $this->filters->insert($filter, $priority);
        return $filter;
    }

    /**
     * Unsubscribe a filter
     * 
     * @param  CallbackHandler $filter 
     * @return bool Returns true if filter found and unsubscribed; returns false otherwise
     */
    public function detach(CallbackHandler $filter)
    {
        return $this->filters->remove($filter);
    }

    /**
     * Retrieve all filters
     * 
     * @return PriorityQueue
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Clear all filters
     * 
     * @return void
     */
    public function clearFilters()
    {
        $this->filters = new PriorityQueue();
    }
}
