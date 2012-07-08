<?php

namespace Zend\Stdlib\StringWrapper;

class MbString extends AbstractStringWrapper
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     * @link http://php.net/manual/mbstring.supported-encodings.php
     */
    protected static $charsets = array(
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
                'PHP extension "mbstring" is required for this adapter'
            );
        }
    }

    public function strlen($str, $charset = 'UTF-8')
    {
        return mb_strlen($str, $charset);
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        return mb_substr($str, $offset, $length, $charset);
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return mb_strpos($haystack, $needle, $offset, $charset);
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        return mb_convert_encoding($str, $toCharset, $fromCharset);
    }
}
