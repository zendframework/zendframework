<?php

namespace Zend\View\Exception;

use Zend\View\Exception;

/**
 * Domain exception
 *
 * @uses       \DomainException
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DomainException extends \DomainException implements Exception
{
}
