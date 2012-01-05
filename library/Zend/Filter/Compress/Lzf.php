<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter\Compress;

use Zend\Filter\Exception;

/**
 * Compression adapter for Lzf
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Lzf implements CompressionAlgorithm
{
    /**
     * Class constructor
     *
     * @param  null $options
     * @return void
     * @throws Exception\ExtensionNotLoadedException if lzf extension missing
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('lzf')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the lzf extension');
        }
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     * @throws Exception\RuntimeException if error occurs during compression
     */
    public function compress($content)
    {
        $compressed = lzf_compress($content);
        if (!$compressed) {
            throw new Exception\RuntimeException('Error during compression');
        }

        return $compressed;
    }

    /**
     * Decompresses the given content
     *
     * @param  string $content
     * @return string
     * @throws Exception\RuntimeException if error occurs during decompression
     */
    public function decompress($content)
    {
        $compressed = lzf_decompress($content);
        if (!$compressed) {
            throw new Exception\RuntimeException('Error during decompression');
        }

        return $compressed;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Lzf';
    }
}
