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
namespace \Zend\URI;
use \Zend\URI\URI;

/**
 * File URI handler
 *
 * The 'file:...' scheme is loosly defined in RFC-1738
 * 
 * @uses      \Zend\URI\URI
 * @uses      \Zend\URI\Exception
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class File extends URI
{
    static protected $_validSchemes = array('file');
    
    /**
     * Convert a UNIX file path to a valid file:// URL
     * 
     * @param  srting $path
     * @return \Zend\URI\File
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
     * @return \Zend\URI\File
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