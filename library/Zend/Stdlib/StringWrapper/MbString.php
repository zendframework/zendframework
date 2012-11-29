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
        'UCS-4',
        'UCS-4BE',
        'UCS-4LE',
        'UCS-2',
        'UCS-2BE',
        'UCS-2LE',
        'UTF-32',
        'UTF-32BE',
        'UTF-32LE',
        'UTF-16',
        'UTF-16BE',
        'UTF-16LE',
        'UTF-7',
        'UTF7-IMAP',
        'UTF-8',
        'ASCII',
        'EUC-JP',
        'SJIS',
        'EUCJP-WIN',
        'SJIS-WIN',
        'ISO-2022-JP',
        'ISO-2022-JP-MS',
        'CP932',
        'CP51932',
        'SJIS-MAC', 'MACJAPANESE', // **
        'SJIS-Mobile#DOCOMO', 'SJIS-DOCOMO', // **
        'SJIS-Mobile#KDDI', 'SJIS-KDDI', // **
        'SJIS-Mobile#SOFTBANK', 'SJIS-SOFTBANK', // **
        'UTF-8-Mobile#DOCOMO', 'UTF-8-DOCOMO', // **
        'UTF-8-Mobile#KDDI-A', // **
        'UTF-8-Mobile#KDDI-B', 'UTF-8-KDDI', // **
        'UTF-8-Mobile#SOFTBANK', 'UTF-8-SOFTBANK', // **
        'ISO-2022-JP-MOBILE#KDDI', 'ISO-2022-JP-KDDI', // **
        'JIS',
        'JIS-MS',
        'CP50220',
        'CP50220RAW',
        'CP50221',
        'CP50222',
        'ISO-8859-1',
        'ISO-8859-2',
        'ISO-8859-3',
        'ISO-8859-4',
        'ISO-8859-5',
        'ISO-8859-6',
        'ISO-8859-7',
        'ISO-8859-8',
        'ISO-8859-9',
        'ISO-8859-10',
        'ISO-8859-13',
        'ISO-8859-14',
        'ISO-8859-15',
        // 'ISO-8859-16',
        'bYTE2BE',
        'bYTE2LE',
        'BYTE4BE',
        'BYTE4LE',
        'BASE64',
        'HTML-ENTITIES',
        '7BIT',
        '8BIT',
        'EUC-CN',
        'CP936',
        'GB18030', // **
        'HZ',
        'EUC-TW',
        'CP950',
        'BIG-5',
        'EUC-KR',
        'UHC', 'CP949',
        'ISO-2022-KR',
        'WINDOWS-1251', 'CP1251',
        'WINDOWS-1252', 'CP1252',
        'CP866', 'IBM866',
        'KOI8-R'
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

        // remove charsets not available before PHP-5.4
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            unset(
                $this->charsets['SJIS-MAC'],
                $this->charsets['MACJAPANESE'],
                $this->charsets['SJIS-Mobile#DOCOMO'],
                $this->charsets['SJIS-DOCOMO'],
                $this->charsets['SJIS-Mobile#KDDI'],
                $this->charsets['SJIS-KDDI'],
                $this->charsets['SJIS-Mobile#SOFTBANK'],
                $this->charsets['SJIS-SOFTBANK'],
                $this->charsets['UTF-8-Mobile#DOCOMO'],
                $this->charsets['UTF-8-DOCOMO'],
                $this->charsets['UTF-8-Mobile#KDDI-A'],
                $this->charsets['UTF-8-Mobile#KDDI-B'],
                $this->charsets['UTF-8-KDDI'],
                $this->charsets['UTF-8-Mobile#SOFTBANK'],
                $this->charsets['UTF-8-SOFTBANK'],
                $this->charsets['ISO-2022-JP-MOBILE#KDDI'],
                $this->charsets['ISO-2022-JP-KDDI'],
                $this->charsets['GB18030']
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
