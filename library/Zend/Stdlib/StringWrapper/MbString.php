<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\StringWrapper;

use Zend\Stdlib\Exception;

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
     * @var null|string[]
     * @link http://php.net/manual/mbstring.supported-encodings.php
     */
    protected static $encodings = null;

    /**
     * Get a list of supported character encodings
     *
     * @return string[]
     */
    public static function getSupportedEncodings()
    {
        if (static::$encodings === null) {
            static::$encodings = array_map('strtoupper', mb_list_encodings());

            // FIXME: Converting € (UTF-8) to ISO-8859-16 gives a wrong result
            $indexIso885916 = array_search('ISO-8859-16', static::$encodings, true);
            if ($indexIso885916 !== false) {
                unset(static::$encodings[$indexIso885916]);
            }
        }

        return static::$encodings;
    }

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct($encoding, $convertEncoding = null)
    {
        if (!extension_loaded('mbstring')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "mbstring" is required for this wrapper'
            );
        }

        parent::__construct($encoding, $convertEncoding);
    }

    /**
     * Returns the length of the given string
     *
     * @param string $str
     * @param string $encoding
     * @return int|false
     */
    public function strlen($str)
    {
        return mb_strlen($str, $this->encoding);
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
    public function substr($str, $offset = 0, $length = null)
    {
        return mb_substr($str, $offset, $length, $this->encoding);
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
    public function strpos($haystack, $needle, $offset = 0)
    {
        return mb_strpos($haystack, $needle, $offset, $this->encoding);
    }

    /**
     * Convert a string from one character encoding to another
     *
     * @param string  $str
     * @param boolean $reverse
     * @return string|false
     */
    public function convert($str, $reverse = false)
    {
        $fromEncoding = $reverse ? $this->convertEncoding : $this->encoding;
        $toEncoding   = $reverse ? $this->encoding : $this->convertEncoding;
        return mb_convert_encoding($str, $toEncoding, $fromEncoding);
    }
}
