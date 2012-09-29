<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Permissions
 */

namespace Zend\Permissions\Rbac;

/**
 * @category   Zend
 * @package    Zend_Permissions
 * @subpackage Rbac
 */
interface AssertionInterface
{
    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac    $bac
     * @return boolean
     */
    public function assert(Rbac $rbac);
}
