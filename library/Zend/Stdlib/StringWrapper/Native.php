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

use Zend\Stdlib\StringUtils;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */
class Native extends AbstractStringWrapper
{
    /**
     * Check if the given encoding is supported
     *
     * @param string $encoding
     * @return boolean
     */
    public function isEncodingSupported($encoding)
    {
        return StringUtils::isSingleByteEncoding($encoding);
    }

    /**
     * Get a list of supported character encodings
     *
     * @return string[]
     */
    public function getSupportedEncodings()
    {
        return StringUtils::getSingleByteEncodings();
    }

    /**
     * Returns the length of the given string
     *
     * @param string $str
     * @param string $encoding
     * @return int|false
     */
    public function strlen($str, $encoding = 'UTF-8')
    {
        return strlen($str);
    }

    /**
     * Returns the portion of string specified by the start and length parameters
     *
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param string   $encoding
     * @return string|false
     */
    public function substr($str, $offset = 0, $length = null, $encoding = 'UTF-8')
    {
        return substr($str, $offset, $length);
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param string $encoding
     * @return int|false
     */
    public function strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8')
    {
        return strpos($haystack, $needle, $offset);
    }
}
