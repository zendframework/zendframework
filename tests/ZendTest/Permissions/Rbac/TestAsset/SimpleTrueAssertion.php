<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Permissions
 */

namespace ZendTest\Permissions\Rbac\TestAsset;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

/**
 * @category   Zend
 * @package    Zend_Permissions
 * @subpackage UnitTests
 * @group      Zend_Rbac
 */
class SimpleTrueAssertion implements AssertionInterface
{
    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac    $bac
     * @return boolean
     */
    public function assert(Rbac $rbac)
    {
        return true;
    }
}
