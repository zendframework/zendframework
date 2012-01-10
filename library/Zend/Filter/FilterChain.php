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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterChain extends AbstractFilter
{
    /**
     * Default priority at which filters are added
     */
    const DEFAULT_PRIORITY = 1000;

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
    public function __construct($options = null)
    {
        $this->filters = new SplPriorityQueue();

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'callbacks':
                    foreach ($value as $spec) {
                        $callback = isset($spec['callback']) ? $spec['callback'] : false;
                        $priority = isset($spec['priority']) ? $spec['priority'] : static::DEFAULT_PRIORITY;
                        if ($callback) {
                            $this->attach($callback, $priority);
                        }
                    }
                    break;
                case 'filters':
                    foreach ($value as $spec) {
                        $name     = isset($spec['name'])     ? $spec['name']     : false;
                        $options  = isset($spec['options'])  ? $spec['options']  : array();
                        $priority = isset($spec['priority']) ? $spec['priority'] : static::DEFAULT_PRIORITY;
                        if ($name) {
                            $this->attachByName($name, $options, $priority);
                        }
                    }
                    break;
                default:
                    // ignore other options
                    break;
            }
        }

        return $this;
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
            $this->broker = $name;
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
     * Attach a filter to the chain
     * 
     * @param  callback|Filter $callback A Filter implementation or valid PHP callback
     * @param  int $priority Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
     * @return FilterChain
     */
    public function attach($callback, $priority = self::DEFAULT_PRIORITY)
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
     * Attach a filter to the chain using a short name
     *
     * Retrieves the filter from the attached plugin broker, and then calls attach() 
     * with the retrieved instance.
     * 
     * @param  string $name 
     * @param  mixed $options 
     * @param  int $priority Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
     * @return FilterChain
     */
    public function attachByName($name, $options = array(), $priority = self::DEFAULT_PRIORITY)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        } else {
            if (range(0, count($options) - 1) != array_keys($options)) {
                $options = array($options);
            }
        }
        $filter = $this->broker($name, $options);
        return $this->attach($filter, $priority);
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
