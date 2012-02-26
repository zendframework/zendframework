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

/**
 * @namespace
 */
namespace Zend\Service\Technorati;
use Zend\Uri;

/**
 * Collection of utilities for various Zend_Service_Technorati classes.
 *
 * @uses       Zend_Date
 * @uses       Zend_Locale
 * @uses       \Zend\Service\Technorati\Exception\RuntimeException
 * @uses       Zend_Uri
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Utils
{
    /**
     * Parses, validates and returns a valid Zend_Uri object
     * from given $input.
     *
     * @param   string|\Zend\Uri\Http $input
     * @return  null|\Zend\Uri\Http
     * @throws  \Zend\Service\Technorati\Exception\RuntimeException
     * @static
     */
    public static function normalizeUriHttp($input)
    {
        // allow null as value
        if ($input === null) {
            return null;
        }

        if ($input instanceof Uri\Http) {
            $uri = $input;
        } else {
            try {
                $uri = Uri\UriFactory::factory((string) $input);
            }
            // wrap exception under Zend_Service_Technorati_Exception object
            catch (\Exception $e) {
                throw new Exception\RuntimeException($e->getMessage(), 0, $e);
            }
        }

        // allow inly Zend_Uri_Http objects or child classes
        if (!($uri instanceof Uri\Http)) {
            throw new Exception\RuntimeException(
                "Invalid URL $uri, only HTTP(S) protocols can be used");
        }

        return $uri;
    }
    /**
     * Parses, validates and returns a valid Zend_Date object
     * from given $input.
     *
     * $input can be either a string, an integer or a Zend_Date object.
     * If $input is string or int, it will be provided to Zend_Date as it is.
     * If $input is a Zend_Date object, the object instance will be returned.
     *
     * @param   mixed|Zend_Date $input
     * @return  null|Zend_Date
     * @throws  \Zend\Service\Technorati\Exception\RuntimeException
     * @static
     */
    public static function normalizeDate($input)
    {
        // allow null as value and return valid Zend_Date objects
        if (($input === null) || ($input instanceof \Zend\Date)) {
            return $input;
        }

        // due to a BC break as of ZF 1.5 it's not safe to use Zend\Date::isDate() here
        // see ZF-2524, ZF-2334
        if (@strtotime($input) !== FALSE) {
            return new \Zend\Date\Date($input);
        } else {
            throw new Exception\RuntimeException("'$input' is not a valid Date/Time");
        }
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
