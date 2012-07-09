<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace Zend\Ldap\Filter;

/**
 * Zend\Ldap\Filter\NotFilter provides a negation filter.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Filter
 */
class NotFilter extends AbstractFilter
{
    /**
     * The underlying filter.
     *
     * @var AbstractFilter
     */
    private $filter;

    /**
     * Creates a Zend\Ldap\Filter\NotFilter.
     *
     * @param AbstractFilter $filter
     */
    public function __construct(AbstractFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Negates the filter.
     *
     * @return AbstractFilter
     */
    public function negate()
    {
        return $this->filter;
    }

    /**
     * Returns a string representation of the filter.
     *
     * @return string
     */
    public function toString()
    {
        return '(!' . $this->filter->toString() . ')';
    }
}
