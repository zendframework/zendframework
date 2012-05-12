<?php
namespace Zend\Crypt\Padding;

interface PaddingInterface {

    /**
     * Pad the string to the specified size
     *
     * @param  string $string    The string to pad
     * @param  int    $blockSize The size to pad to
     * @return string The padded string
     */
    public function pad($string, $blockSize = 32);

    /**
     * Strip the padding from the supplied string
     *
     * @param  string $string The string to trim
     * @return string The unpadded string
     */
    public function strip($string);

}

