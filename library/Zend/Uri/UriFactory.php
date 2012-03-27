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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @namespace
 */
namespace Zend\Uri;
use Zend\Uri\Uri;

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
 * @uses      \Zend\Uri\Uri
 * @uses      \Zend\Uri\Http
 * @uses      \Zend\Uri\File
 * @uses      \Zend\Uri\Mailto
 * @uses      \Zend\Uri\Exception
 * @uses      \Zend\Loader
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class UriFactory
{
    /**
     * Registered scheme-specific classes
     *
     * @var array
     */
    static protected $schemeClasses = array(
        'http'   => 'Zend\Uri\Http',
        'https'  => 'Zend\Uri\Http',
        'mailto' => 'Zend\Uri\Mailto',
        'file'   => 'Zend\Uri\File',
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
        static::$schemeClasses[$scheme] = $class;
    }

    /**
     * Create a URI from a string
     *
     * @param  string $uri
     * @param  string $defaultScheme
     * @return \Zend\Uri\Uri
     */
    static public function factory($uriString, $defaultScheme = null)
    {
        if (!is_string($uriString)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string, received "%s"',
                (is_object($uriString) ? get_class($uriString) : gettype($uriString))
            ));
        }

        $uri    = new Uri($uriString);
        $scheme = strtolower($uri->getScheme());
        if (!$scheme && $defaultScheme) {
            $scheme = $defaultScheme;
        }

        if ($scheme && isset(static::$schemeClasses[$scheme])) {
            $class = static::$schemeClasses[$scheme];
            $uri = new $class($uri);
            if (! $uri instanceof Uri) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'class "%s" registered for scheme "%s" is not a subclass of Zend\Uri\Uri',
                    $class,
                    $scheme
                ));
            }
        }

        return $uri;
    }
}
