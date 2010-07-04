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
namespace Zend\URI;
use Zend\URI\URI;

/**
 * URI Factory Class
 * 
 * The URI factory can be used to generate URI objects from strings, using a 
 * different URI subclass depending on the input URI scheme. New scheme-specific
 * classes can be registered using the registerScheme() method. 
 * 
 * Note that this class contains only static methods and should not be 
 * instanciated 
 *
 * @uses      \Zend\URI\URI
 * @uses      \Zend\URI\HTTP
 * @uses      \Zend\URI\Exception
 * @uses      \Zend\Loader
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Factory 
{
    /**
     * Registered scheme-specific classes
     * 
     * @var array
     */
    static protected $_schemeClasses = array(
        'http'   => '\Zend\URI\HTTP',
        'https'  => '\Zend\URI\HTTP',
        'mailto' => '\Zend\URI\Mailto',
        'file'   => '\Zend\URI\File'
    );
    
    /**
     * Register a scheme-specific class to be used 
     * 
     * @param unknown_type $scheme
     * @param unknown_type $class
     */
    static public function registerScheme($scheme, $class)
    {
        $scheme = strtolower($scheme);
        static::$_schemeClasses[$scheme] = $class;
    }
    
    /**
     * Create a URI from a string
     * 
     * @param  string $uri
     * @param  string $defaultScheme
     * @return \Zend\URI\URI
     */
    static public function factory($uriString, $defaultScheme = null)
    {
        if (! is_string($uriString)) {
            throw new \InvalidArgumentException('Expecting a string, got ' . gettype($uriString));
        }
        
        $uri = new URI($uriString);
        $scheme = strtolower($uri->getScheme());
        if (! $scheme && $defaultScheme) { 
            $scheme = $defaultScheme;
        }
        
        if ($scheme && isset(static::$_schemeClasses[$scheme])) {
            $class = static::$_schemeClasses[$scheme];
            \Zend\Loader::loadClass($class); 
            $uri = new $class($uri);
            if (! $uri instanceof URI) { 
                throw new \InvalidArgumentException("class '$class' registered for scheme '$scheme' is not a subclass of \\Zend\\URI\\URI");
            }
        }
        
        return $uri;
    }
}
