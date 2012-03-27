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
 * Compression adapter for Gzip (ZLib)
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Gz extends AbstractCompressionAlgorithm
{
    /**
     * Compression Options
     * array(
     *     'level'    => Compression level 0-9
     *     'mode'     => Compression mode, can be 'compress', 'deflate'
     *     'archive'  => Archive to use
     * )
     *
     * @var array
     */
    protected $options = array(
        'level'   => 9,
        'mode'    => 'compress',
        'archive' => null,
    );

    /**
     * Class constructor
     *
     * @param null|array|\Traversable $options (Optional) Options to set
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('zlib')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the zlib extension');
        }
        parent::__construct($options);
    }

    /**
     * Returns the set compression level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->options['level'];
    }

    /**
     * Sets a new compression level
     *
     * @param integer $level
     * @return Gz
     */
    public function setLevel($level)
    {
        if (($level < 0) || ($level > 9)) {
            throw new Exception\InvalidArgumentException('Level must be between 0 and 9');
        }

        $this->options['level'] = (int) $level;
        return $this;
    }

    /**
     * Returns the set compression mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->options['mode'];
    }

    /**
     * Sets a new compression mode
     *
     * @param  string $mode Supported are 'compress', 'deflate' and 'file'
     * @return Gz
     * @throws Exceptin\InvalidArgumentException for invalid $mode value
     */
    public function setMode($mode)
    {
        if (($mode != 'compress') && ($mode != 'deflate')) {
            throw new Exception\InvalidArgumentException('Given compression mode not supported');
        }

        $this->options['mode'] = $mode;
        return $this;
    }

    /**
     * Returns the set archive
     *
     * @return string
     */
    public function getArchive()
    {
        return $this->options['archive'];
    }

    /**
     * Sets the archive to use for de-/compression
     *
     * @param  string $archive Archive to use
     * @return Gz
     */
    public function setArchive($archive)
    {
        $this->options['archive'] = (string) $archive;
        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     * @throws Exceptin\RuntimeException if unable to open archive or error during decompression
     */
    public function compress($content)
    {
        $archive = $this->getArchive();
        if (!empty($archive)) {
            $file = gzopen($archive, 'w' . $this->getLevel());
            if (!$file) {
                throw new Exception\RuntimeException("Error opening the archive '" . $this->options['archive'] . "'");
            }

            gzwrite($file, $content);
            gzclose($file);
            $compressed = true;
        } else if ($this->options['mode'] == 'deflate') {
            $compressed = gzdeflate($content, $this->getLevel());
        } else {
            $compressed = gzcompress($content, $this->getLevel());
        }

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
     * @throws Exception\RuntimeException if unable to open archive or error during decompression
     */
    public function decompress($content)
    {
        $archive = $this->getArchive();
        $mode    = $this->getMode();
        if (file_exists($content)) {
            $archive = $content;
        }

        if (file_exists($archive)) {
            $handler = fopen($archive, "rb");
            if (!$handler) {
                throw new Exception\RuntimeException("Error opening the archive '" . $archive . "'");
            }

            fseek($handler, -4, SEEK_END);
            $packet = fread($handler, 4);
            $bytes  = unpack("V", $packet);
            $size   = end($bytes);
            fclose($handler);

            $file       = gzopen($archive, 'r');
            $compressed = gzread($file, $size);
            gzclose($file);
        } else if ($mode == 'deflate') {
            $compressed = gzinflate($content);
        } else {
            $compressed = gzuncompress($content);
        }

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
        return 'Gz';
    }
}
