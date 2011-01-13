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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Filter;

use Zend\Loader\Broker,
    Zend\Stdlib\SplPriorityQueue;

/**
 * @uses       Zend\Filter\Exception
 * @uses       Zend\Filter\AbstractFilter
 * @uses       Zend\Loader
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterChain extends AbstractFilter
{
    /**
     * @var Broker
     */
    protected $broker;

    /**
     * Filter chain
     *
     * @var SplPriorityQueue
     */
    protected $filters;

    /**
     * Initialize filter chain
     * 
     * @return void
     */
    public function __construct()
    {
        $this->filters = new SplPriorityQueue();
    }

    /**
     * Plugin Broker
     *
     * Set or retrieve the plugin broker, or retrieve a specific plugin from it.
     *
     * If $name is null, the broker instance is returned; it will be lazy-loaded
     * if not already present.
     *
     * If $name is a Broker instance, this broker instance will replace or set 
     * the internal broker, and the instance will be returned.
     *
     * If $name is a string, $name and $options will be passed to the broker's 
     * load() method.
     * 
     * @param  null|Broker|string $name 
     * @param array $options 
     * @return Broker|Filter
     */
    public function broker($name = null, $options = array())
    {
        if ($name instanceof Broker) {
            $this->broker = $broker;
            return $this->broker;
        } 

        if (null === $this->broker) {
            $this->broker = new FilterBroker();
        }

        if (null === $name) {
            return $this->broker;
        }

        return $this->broker->load($name, $options);
    }

    /**
     * Connect a filter to the chain
     * 
     * @param  callback|Filter $callback A Filter implementation or valid PHP callback
     * @param  int $priority Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
     * @return FilterChain
     */
    public function connect($callback, $priority = 1000)
    {
        if (!is_callable($callback)) {
            if (!$callback instanceof Filter) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected a valid PHP callback; received "%s"',
                    (is_object($callback) ? get_class($callback) : gettype($callback))
                ));
            }
            $callback = array($callback, 'filter');
        }
        $this->filters->insert($callback, $priority);
        return $this;
    }

    /**
     * Connect a filter to the chain using a short name
     *
     * Retrieves the filter from the attached plugin broker, and then calls connect() 
     * with the retrieved instance.
     * 
     * @param  string $name 
     * @param  mixed $options 
     * @param  int $priority Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
     * @return FilterChain
     */
    public function connectByName($name, $options = array(), $priority = 1000)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        } else {
            if (range(0, count($options) - 1) != array_keys($options)) {
                $options = array($options);
            }
        }
        $filter = $this->broker($name, $options);
        return $this->connect($filter, $priority);
    }

    /**
     * Get all the filters
     *
     * @return SplPriorityQueue
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Returns $value filtered through each filter in the chain
     *
     * Filters are run in the order in which they were added to the chain (FIFO)
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $chain = clone $this->filters;

        $valueFiltered = $value;
        foreach ($chain as $filter) {
            $valueFiltered = call_user_func($filter, $valueFiltered);
        }

        return $valueFiltered;
    }
}
