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
 * @package    Zend_Ldap
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Ldap\Filter;

/**
 * Zend_Ldap_Filter_Logical provides a base implementation for a grouping filter.
 *
 * @uses       \Zend\Ldap\Filter\AbstractFilter
 * @uses       \Zend\Ldap\Filter\Exception
 * @uses       \Zend\Ldap\Filter\StringFilter
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class LogicalFilter extends AbstractFilter
{
    const TYPE_AND = '&';
    const TYPE_OR  = '|';

    /**
     * All the sub-filters for this grouping filter.
     *
     * @var array
     */
    private $_subfilters;

    /**
     * The grouping symbol.
     *
     * @var string
     */
    private $_symbol;

    /**
     * Creates a new grouping filter.
     *
     * @param array  $subfilters
     * @param string $symbol
     */
    protected function __construct(array $subfilters, $symbol)
    {
        foreach ($subfilters as $key => $s) {
            if (is_string($s)) $subfilters[$key] = new StringFilter($s);
            else if (!($s instanceof AbstractFilter)) {
                throw new Exception('Only strings or Zend\Ldap\Filter\AbstractFilter allowed.');
            }
        }
        $this->_subfilters = $subfilters;
        $this->_symbol = $symbol;
    }

    /**
     * Adds a filter to this grouping filter.
     *
     * @param  \Zend\Ldap\Filter\AbstractFilter $filter
     * @return \Zend\Ldap\Filter\LogicalFilter
     */
    public function addFilter(AbstractFilter $filter)
    {
        $new = clone $this;
        $new->_subfilters[] = $filter;
        return $new;
    }

    /**
     * Returns a string representation of the filter.
     *
     * @return string
     */
    public function toString()
    {
        $return = '(' . $this->_symbol;
        foreach ($this->_subfilters as $sub) $return .= $sub->toString();
        $return .= ')';
        return $return;
    }
}
