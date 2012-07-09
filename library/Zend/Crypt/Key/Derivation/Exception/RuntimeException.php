<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Key\Derivation\Exception;

use Zend\Crypt\Exception;

/**
 * Runtime argument exception
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Exception
 */
class RuntimeException extends Exception\RuntimeException implements 
    ExceptionInterface
{}
