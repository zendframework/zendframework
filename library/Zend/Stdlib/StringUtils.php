<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib;

use Zend\Stdlib\StringWrapper\StringWrapperInterface;

/**
 * Utility class for handling strings of different character encodings
 * using available PHP extensions.
 *
 * Declared abstract, as we have no need for instantiation.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 */
abstract class StringUtils
{

    /**
     * Ordered list of registered string wrapper instances
     *
     * @var StringWrapperInterface[]
     */
    protected static $wrapperRegistry;

    /**
     * A list of known single-byte character encodings (upper-case)
     *
     * @var string[]
     */
    protected static $singleByteEncodings = array(
        'ASCII', '7BIT', '8BIT',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-11', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'CP-1251', 'CP-1252',
        // TODO
    );

    /**
     * Get registered wrapper classes
     *
     * @return string[]
     */
    public static function getRegisteredWrappers()
    {
        if (static::$wrapperRegistry === null) {
            static::$wrapperRegistry = array();

            if (extension_loaded('intl')) {
                static::$wrapperRegistry[] = 'Zend\Stdlib\StringWrapper\Intl';
            }

            if (extension_loaded('mbstring')) {
                static::$wrapperRegistry[] = 'Zend\Stdlib\StringWrapper\MbString';
            }

            if (extension_loaded('iconv')) {
                static::$wrapperRegistry[] = 'Zend\Stdlib\StringWrapper\Iconv';
            }

            static::$wrapperRegistry[] = 'Zend\Stdlib\StringWrapper\Native';
        }

        return static::$wrapperRegistry;
    }

    /**
     * Register a string wrapper class
     *
     * @param string $wrapper
     * @return void
     */
    public static function registerWrapper($wrapper)
    {
        $wrapper = (string) $wrapper;
        if (!in_array($wrapper, static::$wrapperRegistry, true)) {
            static::$wrapperRegistry[] = $wrapper;
        }
    }

    /**
     * Unregister a string wrapper class
     *
     * @param string $wrapper
     * @return void
     */
    public static function unregisterWrapper($wrapper)
    {
        $index = array_search((string) $wrapper, static::$wrapperRegistry, true);
        if ($index !== false) {
            unset(static::$wrapperRegistry[$index]);
        }
    }

    /**
     * Get the first string wrapper supporting the given character encoding
     * and supports to convert into the given convert encoding.
     *
     * @param string      $encoding        Character encoding to support
     * @param string|null $convertEncoding OPTIONAL character encoding to convert in
     * @return StringWrapperInterface
     * @throws Exception\RuntimeException If no wrapper supports given character encodings
     */
    public static function getWrapper($encoding = 'UTF-8', $convertEncoding = null)
    {
        foreach (static::getRegisteredWrappers() as $wrapperClass) {
            if ($wrapperClass::isSupported($encoding, $convertEncoding)) {
                return new $wrapperClass($encoding, $convertEncoding);
            }
        }

        throw new Exception\RuntimeException(
            'No wrapper found supporting "' . $encoding . '"'
            . (($convertEncoding !== null) ? ' and "' . $convertEncoding . '"' : '')
        );
    }

    /**
     * Get a list of all known single-byte character encodings
     *
     * @return string[]
     */
    public static function getSingleByteEncodings()
    {
        return static::$singleByteEncodings;
    }

    /**
     * Check if a given encoding is a known single-byte character encoding
     *
     * @param string $encoding
     * @return boolean
     */
    public static function isSingleByteEncoding($encoding)
    {
        return in_array(strtoupper($encoding), static::$singleByteEncodings);
    }

    /**
     * Check if a given string is valid UTF-8 encoded
     *
     * @param string $str
     * @return boolean
     */
    public static function isValidUtf8($str)
    {
        return is_string($str) && ($str === '' || preg_match('/^./su', $str) == 1);
    }
}
