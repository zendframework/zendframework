<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator;

use ArrayObject;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Naming\NamingInterface;
use Zend\Stdlib\Hydrator\StrategyEnabledInterface;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

abstract class AbstractHydrator implements
    HydratorInterface,
    StrategyEnabledInterface,
    FilterEnabledInterface,
    NamingEnabledInterface
{
    /**
     * The list with strategies that this hydrator has.
     *
     * @var ArrayObject
     */
    protected $strategies;

    /**
     * The list of naming strategies that this hydrator has.
     *
     * @var \ArrayObject
     */
    protected $naming;

    /**
     * Composite to filter the methods, that need to be hydrated
     * @var Filter\FilterComposite
     */
    protected $filterComposite;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct()
    {
        $this->naming = new ArrayObject();
        $this->strategies = new ArrayObject();
        $this->filterComposite = new FilterComposite();
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function getNaming($name)
    {
        if (isset($this->naming[$name])) {
            return $this->naming[$name];
        }

        if (!isset($this->naming['*'])) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: no naming by name of "%s", and no wildcard naming present',
                __METHOD__,
                $name
            ));
        }

        return $this->naming['*'];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasNaming($name)
    {
        return array_key_exists($name, $this->naming)
            || array_key_exists('*', $this->naming);
    }

    /**
     * @param                 $name
     * @param NamingInterface $naming
     *
     * @return $this
     */
    public function addNaming($name, NamingInterface $naming)
    {
        $this->naming[$name] = $naming;
        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function removeNaming($name)
    {
        unset($this->naming[$name]);
        return $this;
    }

    /**
     * Gets the strategy with the given name.
     *
     * @param string $name The name of the strategy to get.
     *
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     * @return StrategyInterface
     */
    public function getStrategy($name)
    {
        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        if (!isset($this->strategies['*'])) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: no strategy by name of "%s", and no wildcard strategy present',
                __METHOD__,
                $name
            ));
        }

        return $this->strategies['*'];
    }

    /**
     * Checks if the strategy with the given name exists.
     *
     * @param string $name The name of the strategy to check for.
     * @return bool
     */
    public function hasStrategy($name)
    {
        return array_key_exists($name, $this->strategies)
               || array_key_exists('*', $this->strategies);
    }

    /**
     * Adds the given strategy under the given name.
     *
     * @param string $name The name of the strategy to register.
     * @param StrategyInterface $strategy The strategy to register.
     * @return HydratorInterface
     */
    public function addStrategy($name, StrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
        return $this;
    }

    /**
     * Removes the strategy with the given name.
     *
     * @param string $name The name of the strategy to remove.
     * @return HydratorInterface
     */
    public function removeStrategy($name)
    {
        unset($this->strategies[$name]);
        return $this;
    }

    /**
     * Converts a value for extraction. If no strategy exists the plain value is returned.
     *
     * @param  string $name  The name of the strategy to use.
     * @param  mixed  $value  The value that should be converted.
     * @param  mixed  $object The object is optionally provided as context.
     * @return mixed
     */
    public function extractValue($name, $value, $object = null)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->extract($value, $object);
        }
        return $value;
    }

    /**
     * Converts a value for hydration. If no strategy exists the plain value is returned.
     *
     * @param string $name The name of the strategy to use.
     * @param mixed $value The value that should be converted.
     * @param array $data The whole data is optionally provided as context.
     * @return mixed
     */
    public function hydrateValue($name, $value, $data = null)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->hydrate($value, $data);
        }
        return $value;
    }

    /**
     * @param      $name
     * @param null $object
     *
     * @return mixed
     */
    public function extractName($name, $object = null)
    {
        if ($this->hasNaming($name)) {
            $naming = $this->getNaming($name);
            $name   = $naming->extract($name, $object);
        }
        return $name;
    }

    /**
     * @param $name
     * @param $data
     *
     * @return mixed
     */
    public function hydrateName($name, $data = null)
    {
        if ($this->hasNaming($name)) {
            $naming = $this->getNaming($name);
            $name   = $naming->hydrate($name, $data);
        }
        return $name;
    }

    /**
     * Get the filter instance
     *
     * @return Filter\FilterComposite
     */
    public function getFilter()
    {
        return $this->filterComposite;
    }

    /**
     * Add a new filter to take care of what needs to be hydrated.
     * To exclude e.g. the method getServiceLocator:
     *
     * <code>
     * $composite->addFilter("servicelocator",
     *     function ($property) {
     *         list($class, $method) = explode('::', $property);
     *         if ($method === 'getServiceLocator') {
     *             return false;
     *         }
     *         return true;
     *     }, FilterComposite::CONDITION_AND
     * );
     * </code>
     *
     * @param string $name Index in the composite
     * @param callable|Filter\FilterInterface $filter
     * @param int $condition
     * @return Filter\FilterComposite
     */
    public function addFilter($name, $filter, $condition = FilterComposite::CONDITION_OR)
    {
        return $this->filterComposite->addFilter($name, $filter, $condition);
    }

    /**
     * Check whether a specific filter exists at key $name or not
     *
     * @param string $name Index in the composite
     * @return bool
     */
    public function hasFilter($name)
    {
        return $this->filterComposite->hasFilter($name);
    }

    /**
     * Remove a filter from the composition.
     * To not extract "has" methods, you simply need to unregister it
     *
     * <code>
     * $filterComposite->removeFilter('has');
     * </code>
     *
     * @param $name
     * @return Filter\FilterComposite
     */
    public function removeFilter($name)
    {
        return $this->filterComposite->removeFilter($name);
    }
}
