<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener\Exception;

use Zend\ModuleManager\Exception;

/**
 * Runtime Exception
 * 
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class RuntimeException extends Exception\RuntimeException implements ExceptionInterface
{
}
