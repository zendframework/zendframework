<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Symmetric\Padding;

interface PaddingInterface
{
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
