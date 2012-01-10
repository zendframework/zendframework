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
 * Zend_Ldap_Filter_Not provides a negation filter.
 *
 * @uses       \Zend\Ldap\Filter\AbstractFilter
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class NotFilter extends AbstractFilter
{
    /**
     * The underlying filter.
     *
     * @var \Zend\Ldap\Filter\AbstractFilter
     */
    private $_filter;

    /**
     * Creates a Zend_Ldap_Filter_Not.
     *
     * @param \Zend\Ldap\Filter\AbstractFilter $filter
     */
    public function __construct(AbstractFilter $filter)
    {
        $this->_filter = $filter;
    }

    /**
     * Negates the filter.
     *
     * @return \Zend\Ldap\Filter\AbstractFilter
     */
    public function negate()
    {
        return $this->_filter;
    }

    /**
     * Returns a string representation of the filter.
     *
     * @return string
     */
    public function toString()
    {
        return '(!' . $this->_filter->toString() . ')';
    }
}
