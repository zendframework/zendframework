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
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Uri\Exception;

class InvalidUriPartException 
    extends \InvalidArgumentException
    implements ExceptionInterface
{
    /**
     * Part-specific error codes
     * 
     * @var integer
     */
    const INVALID_SCHEME    = 1;
    const INVALID_USER      = 2;
    const INVALID_PASSWORD  = 4;
    const INVALID_USERINFO  = 6;
    const INVALID_HOSTNAME  = 8;
    const INVALID_PORT      = 16;
    const INVALID_AUTHORITY = 30;
    const INVALID_PATH      = 32;
    const INVALID_QUERY     = 64;
    const INVALID_FRAGMENT  = 128;  
}