<?php

namespace Zend\Stdlib\StringWrapper;

class Iconv extends AbstractStringWrapper
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
        'UTF-8', // TODO
    );

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('iconv')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "iconv" is required for this wrapper'
            );
        }
    }

    public function strlen($str, $charset = 'UTF-8')
    {
        return iconv_strlen($str, $charset);
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        return iconv_substr($str, $offset, $length, $charset);
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return iconv_strpos($haystack, $needle, $offset, $charset);
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        return iconv($fromCharset, $toCharset, $str);
    }
}
