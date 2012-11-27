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
     * Constructor
     */
    public function __construct()
    {
        $this->charsets = StringUtils::getSingleByteCharsets();
    }

    /**
     * Returns the length of the given string
     *
     * @param string $str
     * @param string $charset
     * @return int|false
     */
    public function strlen($str, $charset = 'UTF-8')
    {
        return strlen($str);
    }

    /**
     * Returns the portion of string specified by the start and length parameters
     * 
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param string   $charset
     * @return string|false
     */
    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        return substr($str, $offset, $length);
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param string $charset
     * @return int|false
     */
    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return strpos($haystack, $needle, $offset);
    }

    /**
     * Convert a string from one character encoding to another
     *
     * @param string $str
     * @param string $toCharset
     * @param string $fromCharset
     * @return string|false
     */
    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        return false;
    }
}

