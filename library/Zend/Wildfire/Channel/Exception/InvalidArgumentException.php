<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Wildfire
 */

namespace Zend\Wildfire\Channel\Exception;

use Zend\Wildfire\Exception;

/**
 * Exception for Zend_Wildfire component.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 */
class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{}
