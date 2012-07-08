<?php

namespace Zend\Stdlib\StringAdapter;

class Iconv extends AbstractStringAdapter
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     * @link http://php.net/manual/mbstring.supported-encodings.php
     */
    protected static $charsets = array(
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
                'PHP extension "iconv" is required for this adapter'
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
