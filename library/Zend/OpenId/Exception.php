<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OpenId;

/**
 * Exception class for Zend\OpenId
 *
 * @uses       \Exception
 * @category   Zend
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Exception extends \Exception
{

    /**
     * The specified digest algotithm is not supported by this PHP installation
     */
    const UNSUPPORTED_DIGEST    = 1;

    /**
     * The long math arithmetick is not supported by this PHP installation
     */
    const UNSUPPORTED_LONG_MATH = 2;

    /**
     * Internal long math arithmetic error
     */
    const ERROR_LONG_MATH       = 3;

    /**
     * Iternal storage error
     */
    const ERROR_STORAGE         = 4;
}
