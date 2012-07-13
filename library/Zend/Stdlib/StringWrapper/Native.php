<?php

namespace Zend\Stdlib\StringWrapper;

use Zend\Stdlib\StringUtils;

class Native extends AbstractStringWrapper
{
    public function __construct()
    {
        $this->charsets = StringUtils::getSingleByteCharsets();
    }

    public function strlen($str, $charset = 'UTF-8')
    {
        return strlen($str);
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        return substr($str, $offset, $length);
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return strpos($haystack, $needle, $offset);
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        return false;
    }
}
