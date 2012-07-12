<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\BinaryParser\DataSource;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * Abstract helper class for {@link \Zend\Pdf\BinaryParser\AbstractBinaryParser}
 * that provides the data source for parsing.
 *
 * Concrete subclasses allow for parsing of in-memory, filesystem, and other
 * sources through a common API. These subclasses also take care of error
 * handling and other mundane tasks.
 *
 * Subclasses must implement at minimum {@link __construct()},
 * {@link __destruct()}, {@link readBytes()}, and {@link readAllBytes()}.
 * Subclasses should also override {@link moveToOffset()} and
 * {@link __toString()} as appropriate.
 *
 * The constructor is not defined in this abstract class. However, implementing
 * classes should provide one. It should do the following:
 * - Open the data source for parsing.
 * - Should set $this->_size to the total size in bytes of the data source.
 * - If the data source cannot be opened for any reason (such as insufficient
 *   permissions, missing file, etc.), it should throw an appropriate exception.
 *
 * The destructor is also not defined in this abstract class. However,
 * implementing classes should define one, and have it close the data source.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_BinaryParser
 */
abstract class AbstractDataSource
{
    /**** Instance Variables ****/


    /**
     * Total size in bytes of the data source.
     * @var integer
     */
    protected $_size = 0;

    /**
     * Byte offset of the current read position within the data source.
     * @var integer
     */
    protected $_offset = 0;



    /**** Public Interface ****/


    /* Abstract Methods */

    /**
     * Returns the specified number of raw bytes from the data source at the
     * byte offset of the current read position.
     *
     * Must advance the read position by the number of bytes read by updating
     * $this->_offset.
     *
     * Throws an exception if there is insufficient data to completely fulfill
     * the request or if an error occurs.
     *
     * @param integer $byteCount Number of bytes to read.
     * @return string
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    abstract public function readBytes($byteCount);

    /**
     * Returns the entire contents of the data source as a string.
     *
     * This method may be called at any time and so must preserve the byte
     * offset of the read position, both through $this->_offset and whatever
     * other additional pointers (such as the seek position of a file pointer)
     * that might be used.
     *
     * @return string
     */
    abstract public function readAllBytes();


    /* Object Magic Methods */

    /**
     * Returns a description of the object for debugging purposes.
     *
     * Subclasses should override this method to provide a more specific
     * description of the actual object being represented.
     *
     * @return string
     */
    public function __toString()
    {
        return get_called_class();
    }


    /* Accessors */

    /**
     * Returns the byte offset of the current read position within the data
     * source.
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Returns the total size in bytes of the data source.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->_size;
    }


    /* Primitive Methods */

    /**
     * Moves the current read position to the specified byte offset.
     *
     * Throws an exception you attempt to move before the beginning or beyond
     * the end of the data source.
     *
     * If a subclass needs to perform additional tasks (such as performing a
     * fseek() on a filesystem source), it should do so after calling this
     * parent method.
     *
     * @param integer $offset Destination byte offset.
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function moveToOffset($offset)
    {
        if ($this->_offset == $offset) {
            return;    // Not moving; do nothing.
        }
        if ($offset < 0) {
            throw new Exception\OutOfBoundsException('Attempt to move before start of data source');
        }
        if ($offset >= $this->_size) {    // Offsets are zero-based.
            throw new Exception\OutOfBoundsException('Attempt to move beyond end of data source');
        }
        $this->_offset = $offset;
    }

    /**
     * Shifts the current read position within the data source by the specified
     * number of bytes.
     *
     * You may move forward (positive numbers) or backward (negative numbers).
     * Throws an exception you attempt to move before the beginning or beyond
     * the end of the data source.
     *
     * @param integer $byteCount Number of bytes to skip.
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function skipBytes($byteCount)
    {
        $this->moveToOffset($this->_offset + $byteCount);
    }
}
