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
class Intl extends AbstractStringWrapper
{
    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     */
    protected $encodings = array('UTF-8');

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "intl" is required for this wrapper'
            );
        }
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
        if (strcasecmp($encoding, 'UTF-8') != 0) {
            trigger_error("Character set '{$encoding}' not supported by intl");
            return false;
        }

        return grapheme_strlen($str);
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
        if (strcasecmp($encoding, 'UTF-8') != 0) {
            trigger_error("Character set '{$encoding}' not supported by intl");
            return false;
        }

        return grapheme_substr($str, $offset, $length);
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
        if (strcasecmp($encoding, 'UTF-8') != 0) {
            trigger_error("Character set '{$encoding}' not supported by intl");
            return false;
        }

        return grapheme_strpos($haystack, $needle, $offset);
    }

    /**
     * Convert a string from one character encoding to another
     *
     * @param string $str
     * @param string $toEncoding
     * @param string $fromEncoding
     * @return string|false
     */
    public function convert($str, $toEncoding, $fromEncoding = 'UTF-8')
    {
        if (strcasecmp($toEncoding, $fromEncoding) != 0) {
            trigger_error("Can't convert '{$fromEncoding}' to '{$toEncoding}' using intl", E_WARNING);
            return false;
        }

        return $str;
    }
}
