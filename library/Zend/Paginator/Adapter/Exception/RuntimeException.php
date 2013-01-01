<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
