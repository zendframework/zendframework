<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

use Countable;
use Zend\Stdlib\PriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class FilterChain extends AbstractFilter implements Countable
{
    /**
     * Default priority at which filters are added
     */
    const DEFAULT_PRIORITY = 1000;

    /**
     * @var FilterPluginManager
     */
    protected $plugins;

    /**
     * Filter chain
     *
     * @var PriorityQueue
     */
    protected $filters;

    /**
     * Initialize filter chain
     *
     */
    public function __construct($options = null)
    {
        $this->filters = new PriorityQueue();

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
     * Return the count of attached filters
     *
     * @return int
     */
    public function count()
    {
        return count($this->filters);
    }

    /**
     * Get plugin manager instance
     *
     * @return FilterPluginManager
     */
    public function getPluginManager()
    {
        if (!$this->plugins) {
            $this->setPluginManager(new FilterPluginManager());
        }
        return $this->plugins;
    }

    /**
     * Set plugin manager instance
     *
     * @param  FilterPluginManager $plugins
     * @return FilterChain
     */
    public function setPluginManager(FilterPluginManager $plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }

    /**
     * Retrieve a filter plugin by name
     *
     * @param  mixed $name
     * @param  array $options
     * @return FilterInterface
     */
    public function plugin($name, array $options = array())
    {
        $plugins = $this->getPluginManager();
        return $plugins->get($name, $options);
    }

    /**
     * Attach a filter to the chain
     *
     * @param  callable|FilterInterface $callback A Filter implementation or valid PHP callback
     * @param  int $priority Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
     * @throws Exception\InvalidArgumentException
     * @return FilterChain
     */
    public function attach($callback, $priority = self::DEFAULT_PRIORITY)
    {
        if (!is_callable($callback)) {
            if (!$callback instanceof FilterInterface) {
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
     * Retrieves the filter from the attached plugin manager, and then calls attach()
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
        } elseif (empty($options)) {
            $options = null;
        }
        $filter = $this->getPluginManager()->get($name, $options);
        return $this->attach($filter, $priority);
    }

    /**
     * Merge the filter chain with the one given in parameter
     *
     * @param FilterChain $filterChain
     * @return FilterChain
     */
    public function merge(FilterChain $filterChain)
    {
        foreach ($filterChain->filters as $filter) {
            $this->attach($filter);
        }

        return $this;
    }

    /**
     * Get all the filters
     *
     * @return PriorityQueue
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

    /**
     * Clone filters
     */
    public function __clone()
    {
        $this->filters = clone $this->filters;
    }

    /**
     * Prepare filter chain for serialization
     *
     * Plugin manager (property 'plugins') cannot
     * be serialized. On wakeup the property remains unset
     * and next invokation to getPluginManager() sets
     * the default plugin manager instance (FilterPluginManager).
     */
    public function __sleep()
    {
        return array('filters');
    }
}
