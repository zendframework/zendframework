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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Cache;

/**
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Cache
{

    /**
     * Standard frontends
     *
     * @var array
     */
    public static $standardFrontends = array('Core', 'Output', 'Class', 'File', 'Function', 'Page');

    /**
     * Standard backends
     *
     * @var array
     */
    public static $standardBackends = array(
        'Apc', 
        'BlackHole',
        'File', 
        'Memcached', 
        'Sqlite', 
        'TwoLevels',
        'Xcache', 
        'ZendPlatform', 
        'ZendServer\Disk',
        'ZendServer\ShMem',
    );

    /**
     * Standard backends which implement the ExtendedInterface
     *
     * @var array
     */
    public static $standardExtendedBackends = array('File', 'Apc', 'TwoLevels', 'Memcached', 'Sqlite');

    /**
     * Only for backward compatibility (may be removed in next major release)
     *
     * @var array
     * @deprecated
     */
    public static $availableFrontends = array('Core', 'Output', 'Class', 'File', 'Function', 'Page');

    /**
     * Only for backward compatibility (may be removed in next major release)
     *
     * @var array
     * @deprecated
     */
    public static $availableBackends = array('File', 'Sqlite', 'Memcached', 'Apc', 'ZendPlatform', 'Xcache', 'TwoLevels');

    /**
     * Filter to split camelCased words into individual words
     */
    protected static $_camelCaseFilter;

    /**
     * Consts for clean() method
     */
    const CLEANING_MODE_ALL              = 'all';
    const CLEANING_MODE_OLD              = 'old';
    const CLEANING_MODE_MATCHING_TAG     = 'matchingTag';
    const CLEANING_MODE_NOT_MATCHING_TAG = 'notMatchingTag';
    const CLEANING_MODE_MATCHING_ANY_TAG = 'matchingAnyTag';

    /**
     * Factory
     *
     * @param mixed  $frontend        frontend name (string) or Frontend object
     * @param mixed  $backend         backend name (string) or Backend object
     * @param array  $frontendOptions associative array of options for the corresponding frontend constructor
     * @param array  $backendOptions  associative array of options for the corresponding backend constructor
     * @param boolean $customFrontendNaming if true, the frontend argument is used as a complete class name ; if false, the frontend argument is used as the end of "Zend_Cache_Frontend_[...]" class name
     * @param boolean $customBackendNaming if true, the backend argument is used as a complete class name ; if false, the backend argument is used as the end of "Zend_Cache_Backend_[...]" class name
     * @throws \Zend\Cache\Exception
     * @return Zend\Cache\Frontend
     */
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array(), $customFrontendNaming = false, $customBackendNaming = false)
    {
        if (is_string($backend)) {
            $backendObject = self::_makeBackend($backend, $backendOptions, $customBackendNaming);
        } else {
            if (!is_object($backend) || !in_array('Zend\\Cache\\Backend', class_implements($backend))) {
                self::throwException('backend must be a backend name (string) or an object which implements Zend\\Cache\\Backend');
            }
            $backendObject = $backend;
        }
        if (is_string($frontend)) {
            $frontendObject = self::_makeFrontend($frontend, $frontendOptions, $customFrontendNaming);
        } else {
            if (!is_object($frontend) || !in_array('Zend\\Cache\\Frontend', class_implements($frontend))) {
                self::throwException('frontend must be a frontend name (string) or an object');
            }
            $frontendObject = $frontend;
        }
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }

    /**
     * Backend Constructor
     *
     * @param string  $backend
     * @param array   $backendOptions
     * @param boolean $customBackendNaming
     * @return \Zend\Cache\Backend
     */
    public static function _makeBackend($backend, $backendOptions, $customBackendNaming = false)
    {
        if (!$customBackendNaming) {
            $backend  = self::_normalizeName($backend);
        }
        if (in_array($backend, self::$standardBackends)) {
            // we use a standard backend
            $backendClass = 'Zend\Cache\Backend\\' . $backend;
        } else {
            // we use a custom backend
            if (!preg_match('~^[\w\\\\]+$~D', $backend)) {
                self::throwException("Invalid backend name [$backend]");
            }
            if (!$customBackendNaming) {
                // we use this boolean to avoid an API break
                $backendClass = 'Zend\Cache\Backend\\' . $backend;
            } else {
                $backendClass = $backend;
            }
        }
        $backend = new $backendClass($backendOptions);
        if (!$backend instanceof Backend) {
            self::throwException('Backend must implement Zend\\Cache\\Backend');
        }
        return $backend;
    }

    /**
     * Frontend Constructor
     *
     * @param string  $frontend
     * @param array   $frontendOptions
     * @param boolean $customFrontendNaming
     * @return Zend_Cache_Core|Zend_Cache_Frontend
     */
    public static function _makeFrontend($frontend, $frontendOptions = array(), $customFrontendNaming = false)
    {
        if (!$customFrontendNaming) {
            $frontend = self::_normalizeName($frontend);
        }
        if (in_array($frontend, self::$standardFrontends)) {
            // we use a standard frontend
            // For perfs reasons, with frontend == 'Core', we can interact with the Core itself
            $frontendClass = 'Zend\Cache\Frontend\\' . $frontend;
        } else {
            // we use a custom frontend
            if (!preg_match('~^[\w\\\\]+$~D', $frontend)) {
                self::throwException("Invalid frontend name [$frontend]");
            }
            if (!$customFrontendNaming) {
                // we use this boolean to avoid an API break
                $frontendClass = 'Zend\Cache\Frontend\\' . $frontend;
            } else {
                $frontendClass = $frontend;
            }
        }
        $frontend = new $frontendClass($frontendOptions);
        if (!$frontend instanceof Frontend) {
            self::throwException('Frontend must be implement Zend\\Cache\\Frontend');
        }
        return $frontend;
    }

    /**
     * Throw an exception
     *
     * Note : for perf reasons, the "load" of Zend/Cache/Exception is dynamic
     * @param  string $msg  Message for the exception
     * @throws \Zend\Cache\Exception
     */
    public static function throwException($msg, \Exception $e = null)
    {
        throw new Exception($msg, 0, $e);
    }

    protected static function _getCamelCaseFilter()
    {
        if (null === self::$_camelCaseFilter) {
            self::$_camelCaseFilter = new \Zend\Filter\Word\CamelCaseToSeparator();
        }
        return self::$_camelCaseFilter;
    }

    /**
     * Normalize frontend and backend names to allow multiple words TitleCased
     *
     * @param  string $name  Name to normalize
     * @return string
     */
    protected static function _normalizeName($name)
    {
        $filter = self::_getCamelCaseFilter();
        $name = $filter($name);
        $name = str_replace(array('-', '_', '.'), ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        if (stripos($name, 'ZendServer') === 0) {
            $name = 'ZendServer\\' . substr($name, strlen('ZendServer'));
        }

        return $name;
    }

    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.
     * This function uses the PHP include_path, where PHP's is_readable()
     * does not.
     *
     * Note : this method comes from Zend_Loader (see #ZF-2891 for details)
     *
     * @param string   $filename
     * @return boolean
     */
    private static function _isReadable($filename)
    {
        if (!$fh = @fopen($filename, 'r', true)) {
            return false;
        }
        @fclose($fh);
        return true;
    }

}
