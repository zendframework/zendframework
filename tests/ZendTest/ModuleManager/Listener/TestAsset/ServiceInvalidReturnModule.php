<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager\Listener\TestAsset;

use stdClass;

/**
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage UnitTest
 */
class ServiceInvalidReturnModule
{
    public function getServiceConfiguration()
    {
        return new stdClass;
    }
}
