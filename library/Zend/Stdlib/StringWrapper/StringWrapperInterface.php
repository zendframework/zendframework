<?php

namespace Zend\Stdlib\StringWrapper;

interface StringWrapperInterface
{

    public function isCharsetSupported($charset);

    public function getSupportedCharsets();

    public function strlen($str, $charset = 'UTF-8');

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8');

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8');

    public function convert($str, $toCharset, $fromCharset = 'UTF-8');

    /**
     * Word wrap
     *
     * @param  string  $str
     * @param  integer $width
     * @param  string  $break
     * @param  boolean $cut
     * @param  string  $charset
     * @return string
     */
    public function wordWrap($str, $width = 75, $break = "\n", $cut = false, $charset = 'UTF-8');

    /**
     * String padding
     *
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param  string  $charset
     * @return string
     */
    public function strPad($input, $padLength, $padString = ' ', $padType = \STR_PAD_RIGHT, $charset = 'UTF-8');
}
