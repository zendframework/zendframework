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
class MbString extends AbstractStringWrapper
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     * @link http://php.net/manual/mbstring.supported-encodings.php
     */
    protected $charsets = array(
        'ASCII', '7BIT', '8BIT',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-11', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'UCS-4', 'UCS-4BE', 'UCS-4LE',
        'UCS-2', 'UCS-2BE', 'UCS-2LE',
        'UTF-8', // TODO
    );

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('mbstring')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "mbstring" is required for this wrapper'
            );
        }
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
        return mb_strlen($str, $charset);
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
        return mb_substr($str, $offset, $length, $charset);
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
        return mb_strpos($haystack, $needle, $offset, $charset);
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
        return mb_convert_encoding($str, $toCharset, $fromCharset);
    }
}

