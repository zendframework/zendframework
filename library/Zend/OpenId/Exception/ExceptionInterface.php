<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace Zend\OpenId\Exception;

/**
 * @category   Zend
 * @package    Zend_OpenId
 */
interface ExceptionInterface
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
