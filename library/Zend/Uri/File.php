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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @namespace
 */
namespace Zend\Uri;

/**
 * File URI handler
 *
 * The 'file:...' scheme is loosly defined in RFC-1738
 * 
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class File extends Uri
{
    static protected $_validSchemes = array('file');

    /**
     * Check if the URI is a valid File URI
     * 
     * This applys additional specific validation rules beyond the ones 
     * required by the generic URI syntax
     * 
     * @return boolean
     * @see    \Zend\Uri\Uri::isValid()
     */
    public function isValid()
    {
        
    }
    
    /**
     * User Info part is not used in file URIs
     * 
     * @see    \Zend\Uri\Uri::setUserInfo()
     * @throws InvalidUriPartException
     */
    public function setUserInfo($userInfo)
    {
        
    }
    
    /**
     * Fragment part is not used in file URIs
     * 
     * @see    \Zend\Uri\Uri::setFragment()
     * @throws InvalidUriPartException
     */
    public function setFragment($fragment)
    {
        
    }
    
    /**
     * Convert a UNIX file path to a valid file:// URL
     * 
     * @param  srting $path
     * @return \Zend\Uri\File
     */
    static public function fromUnixPath($path)
    {
        $url = new self('file:');
        if (substr($path, 0, 1) == '/') {
            $url->setHost('');
        }
        $url->setPath($path);
        
        return $url;
    }
    
    /**
     * Convert a Windows file path to a valid file:// URL
     * 
     * @param  string $path
     * @return \Zend\Uri\File
     */
    static public function fromWindowsPath($path)
    {
        $url = new self('file:');

        // Convert directory separators
        $path = str_replace(array('/', '\\'), array('%2F', '/'), $path);
        
        // Is this an absolute path?
        if (preg_match('|^([a-zA-Z]:)?/|', $path)) {
            $url->setHost('');
        }
        $url->setPath($path);
    }
}
