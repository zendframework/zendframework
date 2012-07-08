<?php

namespace Zend\Stdlib\StringWrapper;

use Zend\Stdlib\StringUtils;

class Native extends AbstractStringWrapper
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     * @link http://php.net/manual/mbstring.supported-encodings.php
     */
    protected static $charsets = array(
        'ASCII',
        'UTF-7', 'UTF-8', 'UTF-16', 'UTF-32',
        'UCS-2', 'UCS-2BE', 'UCS-2LE',
        'UCS-4', 'UCS-4BE', 'UCS-4LE',
    );

    public function strlen($str, $charset = 'UTF-8')
    {
        if (StringUtils::isSingleByteCharset($charset)) {
            return strlen($str);
        }

        $charset = strtoupper($charset);
        if ($charset == 'UTF-8') {
            // replace multibyte characters with 1 byte and count bytes
            return strlen(preg_replace('/('
                . '[\xc0-\xdf][\x80-\xbf]'     // 2 bytes (110xxxxx 10xxxxxx)
                . '|[\xe0-\xef][\x80-\xbf]{2}' // 3 bytes (1110xxxx [10xxxxxx, ...])
                . '|[\xf0-\xf7][\x80-\xbf]{3}' // 4 bytes (11110xxx [10xxxxxx, ...])
                . '|[\xf8-\xfb][\x80-\xbf]{4}' // 5 bytes (111110xx [10xxxxxx, ...])
                . '|[\xfd-\xfe][\x80-\xbf]{5}' // 6 bytes (1111110x [10xxxxxx, ...])
                . '|\xfe[\x80-\xbf]{6}'        // 7 bytes (11111110 [10xxxxxx, ...])
                . ')/s', ' ', $str));
        } elseif ($charset == 'UTF-7') {
            // TODO
        } elseif ($charset == 'UTF-16' || $charset == 'UCS-2' || $charset == 'UCS-2BE' || $charset == 'UCS-2LE') {
            return ceil(strlen($str) / 2);
        } elseif ($charset == 'UTF-32' || $charset == 'UCS-4' || $charset == 'UCS-4BE' || $charset == 'UCS-4LE') {
            return ceil(strlen($str) / 4);
        }

        return false;
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        if (StringUtils::isSingleByteCharset($charset)) {
            return substr($str, $offset, $length);
        }

        $charset = strtoupper($charset);
        if ($charset == 'UTF-8') {
            // TODO
        } elseif ($charset == 'UTF-7') {
            // TODO
        } elseif ($charset == 'UTF-16' || $charset == 'UCS-2') {
            return substr($str, $offset * 2, $length * 2);
        } elseif ($charset == 'UTF-32' || $charset == 'UCS-4') {
            return substr($str, $offset * 4, $length * 4);
        }

        return false;
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        if (StringUtils::isSingleByteCharset($charset)) {
            return strpos($haystack, $needle, $offset);
        }

        $charset = strtoupper($charset);
        if ($charset == 'UTF-8') {
            // TODO
        } elseif ($charset == 'UTF-7') {
            // TODO
        } elseif ($charset == 'UTF-16' || $charset == 'UCS-2') {
            // TODO
        } elseif ($charset == 'UTF-32' || $charset == 'UCS-4') {
            // TODO
        }

        return false;
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        $fromName = str_replace('-', '', strtolower($fromCharset));
        $toName   = str_replace('-', '', strtolower($toCharset));
        $method   = 'convert' . $fromName . 'To' . $toName;

        if (method_exists($this, $method)) {
            return $this->$method($str);
        }

        return false;
    }

    public function convertAsciiToUtf8($str)
    {
        return $str;
    }

    public function convertAsciiToUtf16($str)
    {
        return preg_replace_callback("/./", function ($char) {
            return "\0" . $char;
        }, $str);
    }

    public function convertAsciiToUcs2($str)
    {
        return $this->convertAsciiToUtf16($str);
    }

    public function convertAsciiToUtf32($str)
    {
        return preg_replace_callback("/./", function ($char) {
            return "\0\0\0" . $char;
        }, $str);
    }

    public function convertAsciiToUcs4($str)
    {
        return $this->convertAsciiToUtf32($str);
    }

    public function convertUtf8ToAscii($str)
    {
        // TODO
        return $str;
    }

    public function convertUtf8ToUtf16($str)
    {
        // TODO
        return $str;
    }

    public function convertUtf8ToUcs2($str)
    {
        return $this->convertUtf8ToUtf16($str);
    }

    public function convertUtf8ToUtf32($str)
    {
        // TODO
        return $str;
    }

    public function convertUtf8ToUcs4($str)
    {
        return $this->convertUtf8ToUtf32($str);
    }
}
