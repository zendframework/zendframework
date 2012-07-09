<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

/**
 * A wrapper for strings for buffered reading.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class MimeFile
{

    /**
     * A handle to the file that is part of the message.
     *
     * @var resource
     */
    protected $_fileHandle = null;

    /**
     * Create a new MimeFile object.
     *
     * @param string $fileHandle An open file handle to the file being
     *               read.
     */
    public function __construct($fileHandle)
    {
        $this->_fileHandle = $fileHandle;
    }

    /**
     * Read the next chunk of the file.
     *
     * @param integer $bytesRequested The size of the chunk that is to be read.
     * @return string A corresponding piece of the message. This could be
     *                binary or regular text.
     */
    public function read($bytesRequested)
    {
      return fread($this->_fileHandle, $bytesRequested);
    }

}
