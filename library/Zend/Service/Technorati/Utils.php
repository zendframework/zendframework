<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Technorati;

use DateTime;
use Zend\Uri;

/**
 * Collection of utilities for various Zend\Service\Technorati classes.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
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

                if (isset($traces[2])) {
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
     * Parses, validates and returns a valid DateTime object
     * from given $input.
     *
     * $input can be either a string, an integer or a DateTime object.
     * If $input is string or int, it will be provided to DateTime as it is.
     * If $input is a DateTime object, the object instance will be returned.
     *
     * @param   mixed|DateTime $input
     * @return  DateTime
     * @throws  \Exception
     * @static
     */
    public static function normalizeDate($input)
    {
        // allow null as value and return valid DateTime objects
        if (($input === null) || ($input instanceof DateTime)) {
            return $input;
        }

        return new DateTime($input);
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
