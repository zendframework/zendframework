<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */
namespace Zend\Stdlib\Hydrator\Filter;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 */
class FilterComposite implements FilterInterface
{
    /**
     * @var \ArrayObject
     */
    protected $orFilter;
    /**
     * @var \ArrayObject
     */
    protected $andFilter;

    /**
     * Constant to add with "or" conditition
     */
    const CONDITION_OR = 1;
    /**
     * Constant to add with "and" conditition
     */
    const CONDITION_AND = 2;

    /**
     * Define default Filter
     */
    public function __construct($orFilter = array(), $andFilter = array())
    {
        $this->orFilter = new \ArrayObject($orFilter);
        $this->andFilter = new \ArrayObject($andFilter);
    }

    /**
     * Add a filter to the composite. Has to be indexed with $name in
     * order to identify a specific filter.
     *
     * This example will exclude all methods from the hydration, that starts with 'getService'
     * <code>
     * $composite->addFilter('exclude',
     *     function($method) {
     *         if (preg_match('/^getService/', $method) {
     *             return false;
     *         }
     *         return true;
     *     }, FilterComposite::CONDITION_AND
     * );
     * </code>
     *
     * @param string $name
     * @param callable|FilterInterface $filter
     * @param int $condition Can be either FilterComposite::CONDITION_OR or FilterComposite::CONDITION_AND
     */
    public function addFilter($name, $filter, $condition = self::CONDITION_OR)
    {
        if (
            is_callable($filter) ||
            $filter instanceof FilterInterface
        ) {
            if ($condition === self::CONDITION_OR) {
                $this->orFilter[$name] = $filter;
            }
            if ($condition === self::CONDITION_AND) {
                $this->andFilter[$name] = $filter;
            }
        }
    }

    /**
     * Remove a filter from the composition
     *
     * @param $name string Identifier for the filter
     */
    public function removeFilter($name)
    {
        if( isset($this->orFilter[$name])) {
            unset($this->orFilter[$name]);
        }

        if( isset($this->andFilter[$name])) {
            unset($this->andFilter[$name]);
        }
    }

    /**
     * Check if $name has a filter registered
     *
     * @param $name string Identifier for the filter
     * @return bool
     */
    public function hasFilter($name)
    {
        return
            isset($this->orFilter[$name]) || isset($this->andFilter[$name]);
    }

    /**
     * Filter the composite based on the AND and OR condition
     * Will return true if one from the "or conditions" and all from
     * the "and condition" returns true. Otherwise false
     *
     * @param $property string Parameter will be e.g. Parent\Namespace\Class::method
     * @return bool
     */
    public function filter($property)
    {
        // return true if no filters are registered
        if (
            count($this->orFilter) === 0 &&
            count($this->andFilter) === 0
        ) {
            return true;
        }

        // Check if 1 from the or filters return true
        $returnValue = false;
        foreach($this->orFilter as $filter) {
            if (is_callable($filter)) {
                if( $filter($property) === true)
                {
                    $returnValue = true;
                    break;
                }
            } else {
                if ( $filter->filter($property) === true) {
                    $returnValue = true;
                    break;
                }
            }
        }

        // Check if all of the and condition return true
        foreach($this->andFilter as $filter) {
            if (is_callable($filter)) {
                if( $filter($property) === false) {
                    return false;
                }
            } else {
                if( $filter->filter($property) === false) {
                    return false;
                }
            }
        }

        return $returnValue;
    }
}
