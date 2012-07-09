<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\Adapter\Exception;

use Zend\Paginator\Exception;

/**
 * @category   Zend
 * @package    Zend\Paginator\Adapter
 * @subpackage Exception
 */
class RuntimeException extends Exception\RuntimeException implements 
    ExceptionInterface
{}
