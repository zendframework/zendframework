<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Storage\Part\Exception;

use Zend\Mail\Storage\Exception;

/**
 * Exception for Zend_Mail component.
 *
 * @category   Zend
 * @subpackage Storage
 * @package    Zend_Mail
 */
class RuntimeException extends Exception\RuntimeException implements
    ExceptionInterface
{}
