<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\StringWrapper;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */
interface StringWrapperInterface
{

    /**
     * Check if the given character encoding is supported
     *
     * @param string $encoding
     * @return boolean
     */
    public function isEncodingSupported($encoding);

    /**
     * Get a list of supported character encodings
     *
     * @return string[]
     */
    public function getSupportedEncodings();

    /**
     * Returns the length of the given string
     *
     * @param string $str
     * @param string $encoding
     * @return int|false
     */
    public function strlen($str, $encoding = 'UTF-8');

    /**
     * Returns the portion of string specified by the start and length parameters
     *
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param string   $encoding
     * @return string|false
     */
    public function substr($str, $offset = 0, $length = null, $encoding = 'UTF-8');

    /**
     * Find the position of the first occurrence of a substring in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param string $encoding
     * @return int|false
     */
    public function strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8');

    /**
     * Convert a string from one character encoding to another
     *
     * @param string $str
     * @param string $toEncoding
     * @param string $fromEncoding
     * @return string|false
     */
    public function convert($str, $toEncoding, $fromEncoding = 'UTF-8');

    /**
     * Wraps a string to a given number of characters
     *
     * @param  string  $str
     * @param  integer $width
     * @param  string  $break
     * @param  boolean $cut
     * @param  string  $encoding
     * @return string
     */
    public function wordWrap($str, $width = 75, $break = "\n", $cut = false, $encoding = 'UTF-8');

    /**
     * Pad a string to a certain length with another string
     *
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param  string  $encoding
     * @return string
     */
    public function strPad($input, $padLength, $padString = ' ', $padType = \STR_PAD_RIGHT, $encoding = 'UTF-8');
}
