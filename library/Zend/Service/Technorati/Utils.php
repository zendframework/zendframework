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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Technorati;

use Zend\Uri,
    Zend\Date\Date as ZendDate;

/**
 * Collection of utilities for various Zend\Service\Technorati classes.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Utils
{
    /**
     * Parses, validates and returns a valid Zend\Uri object
     * from given $input.
     *
     * @param   string|Uri\Http $input
     * @return  null|Uri\Http
     * @throws  Exception\RuntimeException
     * @static
     */
    public static function normalizeUriHttp($input)
    {
        // allow null as value
        if ($input === null) {
            return null;
        }

        $uri = $input;

        // Try to cast
        if (!$input instanceof Uri\Http) {
            try {
                $uri = Uri\UriFactory::factory((string) $input);
            } catch (\Exception $e) {
                // wrap exception under Exception object
                throw new Exception\RuntimeException($e->getMessage(), 0, $e);
            }
        }

        // allow only Zend\Uri\Http objects or child classes (not other URI formats)
        if (!$uri instanceof Uri\Http) {
            throw new Exception\RuntimeException(sprintf(
                "%s: Invalid URL %s, only HTTP(S) protocols can be used",
                __METHOD__,
                $uri
            ));
        }

        // Validate the URI
        if (!$uri->isValid()) {
            $caller = function () { 
                $traces = debug_backtrace(); 

                if (isset($traces[2])) 
                { 
                    return $traces[2]['function']; 
                } 

                return null; 
            };
            throw new Exception\RuntimeException(sprintf(
                '%s (called by %s): invalid URI ("%s") provided',
                __METHOD__,
                $caller(),
                (string) $input
            ));
        }

        return $uri;
    }

    /**
     * Parses, validates and returns a valid ZendDate object
     * from given $input.
     *
     * $input can be either a string, an integer or a ZendDate object.
     * If $input is string or int, it will be provided to ZendDate as it is.
     * If $input is a ZendDate object, the object instance will be returned.
     *
     * @param   mixed|Date $input
     * @return  null|Date
     * @throws  Exception\RuntimeException
     * @static
     */
    public static function normalizeDate($input)
    {
        // allow null as value and return valid ZendDate objects
        if (($input === null) || ($input instanceof ZendDate)) {
            return $input;
        }

        // due to a BC break as of ZF 1.5 it's not safe to use ZendDate::isDate() here
        // see ZF-2524, ZF-2334
        set_error_handler(function () { return true; }, E_NOTICE|E_WARNING|E_STRICT);
        if (strtotime($input) === FALSE) {
            restore_error_handler();
            throw new Exception\RuntimeException(sprintf(
                '%s: "%s" is not a valid Date/Time',
                __METHOD__, 
                (string) $input
            ));
        }
        restore_error_handler();

        return new ZendDate($input);
    }

    /**
     * @todo public static function xpathQueryAndSet() {}
     */

    /**
     * @todo public static function xpathQueryAndSetIf() {}
     */

    /**
     * @todo public static function xpathQueryAndSetUnless() {}
     */
}
