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
 * @package    Zend_PDF
 * @subpackage Zend_PDF_BinaryParser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\BinaryParser\DataSource;
use Zend\Pdf\Exception;
use Zend\Pdf;

/**
 * Concrete subclass of {@link \Zend\Pdf\BinaryParser\DataSource\AbstractDataSource}
 * that provides an interface to filesystem objects.
 *
 * Note that this class cannot be used for other sources that may be supported
 * by {@link fopen()} (through URL wrappers). It may be used for local
 * filesystem objects only.
 *
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\BinaryParser\DataSource\AbstractDataSource
 * @package    Zend_PDF
 * @subpackage Zend_PDF_BinaryParser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class File extends AbstractDataSource
{
    /**** Instance Variables ****/


    /**
     * Fully-qualified path to the file.
     * @var string
     */
    protected $_filePath = '';

    /**
     * File resource handle .
     * @var resource
     */
    protected $_fileResource = null;



    /**** Public Interface ****/


    /* Concrete Class Implementation */

    /**
     * Object constructor.
     *
     * Validates the path to the file, ensures that it is readable, then opens
     * it for reading.
     *
     * Throws an exception if the file is missing or cannot be opened.
     *
     * @param string $filePath Fully-qualified path to the file.
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($filePath)
    {
        if (! (is_file($filePath) || is_link($filePath))) {
            throw new Exception\IOException('Invalid file path: ' . $filePath);
        }
        if (! is_readable($filePath)) {
            throw new Exception\IOException('File is not readable: ' . $filePath);
        }
        if (($this->_size = @filesize($filePath)) === false) {
            throw new Exception\IOException('Error while obtaining file size: ' . $filePath);
        }
        if (($this->_fileResource = @fopen($filePath, 'rb')) === false) {
            throw new Exception\IOException('Cannot open file for reading: ' . $filePath);
        }
        $this->_filePath = $filePath;
    }

    /**
     * Object destructor.
     *
     * Closes the file if it had been successfully opened.
     */
    public function __destruct()
    {
        if (is_resource($this->_fileResource)) {
            @fclose($this->_fileResource);
        }
    }

    /**
     * Returns the specified number of raw bytes from the file at the byte
     * offset of the current read position.
     *
     * Advances the read position by the number of bytes read.
     *
     * Throws an exception if an error was encountered while reading the file or
     * if there is insufficient data to completely fulfill the request.
     *
     * @param integer $byteCount Number of bytes to read.
     * @return string
     * @throws \Zend\Pdf\Exception
     */
    public function readBytes($byteCount)
    {
        $bytes = @fread($this->_fileResource, $byteCount);
        if ($bytes === false) {
            throw new Exception\IOException('Unexpected error while reading file');
        }
        if (strlen($bytes) != $byteCount) {
            throw new Exception\IOException('Insufficient data to read ' . $byteCount . ' bytes');
        }
        $this->_offset += $byteCount;
        return $bytes;
    }

    /**
     * Returns the entire contents of the file as a string.
     *
     * Preserves the current file seek position.
     *
     * @return string
     */
    public function readAllBytes()
    {
        return file_get_contents($this->_filePath);
    }


    /* Object Magic Methods */

    /**
     * Returns the full filesystem path of the file.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_filePath;
    }


    /* Primitive Methods */

    /**
     * Seeks the file read position to the specified byte offset.
     *
     * Throws an exception if the file pointer cannot be moved or if it is
     * moved beyond EOF (end of file).
     *
     * @param integer $offset Destination byte offset.
     * @throws \Zend\Pdf\Exception
     */
    public function moveToOffset($offset)
    {
        if ($this->_offset == $offset) {
            return;    // Not moving; do nothing.
        }
        parent::moveToOffset($offset);
        $result = @fseek($this->_fileResource, $offset, SEEK_SET);
        if ($result !== 0) {
            throw new Exception\IOException('Error while setting new file position');
        }
        if (feof($this->_fileResource)) {
            throw new Exception\IOException('Moved beyond the end of the file');
        }
    }
}
