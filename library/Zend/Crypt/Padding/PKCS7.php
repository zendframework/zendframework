<?php

namespace Zend\Crypt\Padding;

class PKCS7 implements PaddingInterface {

    /**
     * Pad the string to the specified size
     *
     * @param string $string    The string to pad
     * @param int    $blockSize The size to pad to
     *
     * @return string The padded string
     */
    public function pad($string, $blockSize = 32) {
        $pad = $blockSize - (strlen($string) % $blockSize);
        return $string . str_repeat(chr($pad), $pad);
    }

    /**
     * Strip the padding from the supplied string
     *
     * @param string $string The string to trim
     *
     * @return string The unpadded string
     */
    public function strip($string) {
        $end  = substr($string, -1);
        $last = ord($end);
        $len  = strlen($string) - $last;
        if (substr($string, $len) == str_repeat($end, $last)) {
            return substr($string, 0, $len);
        }
        return false;
    }

}
