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
 * Zend\Ldap\Filter\AndFilter provides an 'and' filter.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Filter
 */
class AndFilter extends AbstractLogicalFilter
{
    /**
     * Creates an 'and' grouping filter.
     *
     * @param array $subfilters
     */
    public function __construct(array $subfilters)
    {
        parent::__construct($subfilters, self::TYPE_AND);
    }
}
