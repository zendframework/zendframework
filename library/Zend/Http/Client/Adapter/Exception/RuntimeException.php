<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Client\Adapter\Exception;

use Zend\Http\Client\Exception;

/**
 *
 * @category  Zend
 * @package   Zend_Application
 */
class RuntimeException extends Exception\RuntimeException implements
    ExceptionInterface
{}
