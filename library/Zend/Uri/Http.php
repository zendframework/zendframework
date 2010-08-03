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
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @namespace
 */
namespace Zend\Uri;

/**
 * HTTP URI handler
 *
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Http extends Uri
{
    static protected $_validSchemes = array('http', 'https');
    
    /**
     * Check if the URI is a valid HTTP URI
     * 
     * This applys additional HTTP specific validation rules beyond the ones 
     * required by the generic URI syntax
     * 
     * @return boolean
     * @see    \Zend\Uri\Uri::isValid()
     */
    public function isValid()
    {
        
    }

    /**
     * Get the username part (before the ':') of the userInfo URI part
     * 
     * @return string
     */
    public function getUser()
    {
        
    }
    
    /**
     * Get the password part (after the ':') of the userInfo URI part
     * 
     * @return string
     */
    public function getPassword()
    {
        
    }

    /**
     * Set the username part (before the ':') of the userInfo URI part
     * 
     * @param  string $user
     * @return \Zend\Uri\Http
     */
    public function setUser($user)
    {
        
    }
    
    /**
     * Set the password part (after the ':') of the userInfo URI part
     * 
     * @param  string $password
     * @return \Zend\Uri\Http
     */
    public function setPassword($password)
    {
        
    }
    
    /**
     * Validate the host part of an HTTP URI
     * 
     * Unlike the generic URI syntax, HTTP URIs do not allow IPv6 or reg-name
     * URIs, only IPv4 and DNS compatible host names.
     * 
     * @param  string $host
     * @return boolean
     */
    static public function validateHost($host)
    {
        
    }
}