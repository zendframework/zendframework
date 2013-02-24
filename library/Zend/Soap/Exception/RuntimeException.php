<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace Zend\Soap\Exception;

/**
 * Exception thrown when there is an error during program execution
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Client
 */
class RuntimeException
    extends \RuntimeException
    implements ExceptionInterface
{}
