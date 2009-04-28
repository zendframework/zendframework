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
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * A streaming Media MIME class that allows for buffered read operations.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_MediaMimeStream
{

    /**
     * The Content-Type section that precedes the XML data in the message.
     *
     * @var string
     */
    // TODO (jhartmann) Add support for charset [ZF-5768]
    const XML_HEADER = "Content-Type: application/atom+xml\r\n\r\n";

    /**
     * A constant indicating the xml string part of the message
     *
     * @var integer
     */
    const PART_XML_STRING = 0;

    /**
     * A constant indicating the file binary part of the message
     *
     * @var integer
     */
    const PART_FILE_BINARY = 1;

    /**
     * A constant indicating the closing boundary string of the message
     *
     * @var integer
     */
    const PART_CLOSING_XML_STRING = 2;

    /**
     * The maximum buffer size that can be used.
     *
     * @var integer
     */
    const MAX_BUFFER_SIZE = 8192;

    /**
     * A valid MIME boundary including a linefeed.
     *
     * @var string
     */
    protected $_boundaryLine = null;

    /**
     * A valid MIME boundary without a linefeed for use in the header.
     *
     * @var string
     */
    protected $_boundaryString = null;

    /**
     * A valid MIME closing boundary including a linefeed.
     *
     * @var string
     */
    protected $_closingBoundaryLine = null;

    /**
     * A handle to the file that is part of the message.
     *
     * @var resource
     */
    protected $_fileHandle = null;

    /**
     * The headers that preceed the file binary including linefeeds.
     *
     * @var string
     */
    protected $_fileHeaders = null;

    /**
     * The internet media type of the enclosed file.
     *
     * @var string
     */
    protected $_fileContentType = null;

    /**
     * The file size.
     *
     * @var integer
     */
    protected $_fileSize = null;

    /**
     * The total size of the message.
     *
     * @var integer
     */
    protected $_totalSize = null;

    /**
     * The XML string that typically represents the entry to be sent.
     *
     * @var string
     */
    protected $_xmlString = null;

    /**
     * The number of bytes that have been read so far.
     *
     * @var integer
     */
    protected $_bytesRead = 0;

    /**
     * Enumeration indicating the part of the message that is currently being
     * read. Allowed values are: 0, 1 and 2, corresponding to the constants:
     * PART_XML_STRING, PART_FILE_BINARY, PART_CLOSING_XML_STRING
     *
     * @var integer
     */
    protected $_currentPart = 0;

    /**
     * A nested array containing the message to be sent. Each element contains
     * an array in the format:
     *
     * [integer (size of message)][string (message)]
     *
     * Note that the part corresponding to the file only contains a size.
     *
     * @var array
     */
    protected $_parts = null;

    /**
     * A boolean to be set immediately once we have finished reading.
     *
     * @var boolean
     */
    protected $_doneReading = false;

    /**
     * Create a new MimeMediaStream object.
     *
     * @param string $xmlString The string corresponding to the XML section
     *               of the message, typically an atom entry or feed.
     * @param string $filePath The path to the file that constitutes the binary
     *               part of the message.
     * @param string $fileContentType The valid internet media type of the file.
     * @throws Zend_Gdata_App_IOException If the file cannot be read or does
     *         not exist. Also if mbstring.func_overload has been set > 1.
     */
    public function __construct($xmlString = null, $filePath = null,
        $fileContentType = null)
    {
        $this->_xmlString = $xmlString;
        $this->_filePath = $filePath;
        $this->_fileContentType = $fileContentType;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            require_once 'Zend/Gdata/App/IOException.php';
            throw new Zend_Gdata_App_IOException('File to be uploaded at ' .
                $filePath . ' does not exist or is not readable.');
        }

        $this->_fileHandle = fopen($filePath, 'rb', true);
        $this->generateBoundaries();
        $this->calculatePartSizes();
    }

    /**
     * Generate the MIME message boundary strings.
     *
     * @return void
     */
    private function generateBoundaries()
    {
        $this->_boundaryString = '=_' . md5(microtime(1) . rand(1,20));
        $this->_boundaryLine = "\r\n" . '--' . $this->_boundaryString . "\r\n";
        $this->_closingBoundaryLine = "\r\n" . '--' . $this->_boundaryString .
            '--';
    }

    /**
     * Calculate the sizes of the MIME message sections.
     *
     * @return void
     */
    private function calculatePartSizes()
    {
        $this->_fileHeaders = 'Content-Type: ' . $this->_fileContentType .
            "\r\n" . 'Content-Transfer-Encoding: binary' . "\r\n\r\n";
        $this->_fileSize = filesize($this->_filePath);

        $stringSection = $this->_boundaryLine . self::XML_HEADER .
            $this->_xmlString . "\r\n" . $this->_boundaryLine .
            $this->_fileHeaders;
        $stringLen = strlen($stringSection);
        $closingBoundaryLen = strlen($this->_closingBoundaryLine);

        $this->_parts = array();
        $this->_parts[] = array($stringLen, $stringSection);
        $this->_parts[] = array($this->_fileSize);
        $this->_parts[] = array($closingBoundaryLen,
            $this->_closingBoundaryLine);

        $this->_totalSize = $stringLen + $this->_fileSize + $closingBoundaryLen;
    }

    /**
     * A wrapper around fread() that doesn't error when $length is 0.
     *
     * @param integer $length Number of bytes to read.
     * @return string Results of byte operation.
     */
    private function smartfread($length)
    {
        if ($length < 1) {
            return '';
        } else {
            return fread($this->_fileHandle, $length);
        }
    }

    /**
     * A non mbstring overloadable strlen-like function.
     *
     * @param string $string The string whose length we want to get.
     * @return integer The length of the string.
     */
    private function strlen2($string)
    {
        return array_sum(char_count($string));
    }

    /**
     * Read a specific chunk of the the MIME multipart message.
     *
     * This function works by examining the internal 'parts' array. It
     * expects that array to consist of basically a string, a file handle
     * and a closing string.
     *
     * An abbreviated version of what this function does is as follows:
     *
     * - throw exception if trying to read bigger than the allocated max buffer
     * - If bufferSize bigger than the entire message: return it and exit.
     *
     * - Check how far to read by looking at how much has been read.
     * - Figure out whether we are crossing sections in this read:
     *   i.e. -> reading past the xml_string and into the file ?
     *   - Determine whether we are crossing two sections in this read:
     *     i.e. xml_string, file and half of the closing string or
     *     possibly file, closing string and next (non-existant) section
     *     and handle each case.
     *   - If we are NOT crossing any sections: read either string and
     *     increment counter, or read file (no counter needed since fread()
     *     stores it's own counter.
     *   - If we are crossing 1 section, figure out how much remains in that
     *     section that we are currently reading and how far to read into
     *     the next section. If the section just read is xml_string, then
     *     immediately unset it from our 'parts' array. If it is the file,
     *     then close the handle.
     *
     * @param integer $bufferSize The size of the chunk that is to be read,
     *                            must be lower than MAX_BUFFER_SIZE.
     * @throws Zend_Gdata_App_InvalidArgumentException if buffer size too big.
     * @return string A corresponding piece of the message. This could be
     *                binary or regular text.
     */
    public function read($bufferSize)
    {
        if ($bufferSize > self::MAX_BUFFER_SIZE) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Buffer size ' .
                'is larger than the supported max of ' . self::MAX_BUFFER_SIZE);
        }

        // handle edge cases where bytesRead is negative
        if ($this->_bytesRead < 0) {
            $this->_bytesRead = 0;
        }

        $returnString = null;
        // If entire message is smaller than the buffer, just return everything
        if ($bufferSize > $this->_totalSize) {
            $returnString = $this->_parts[self::PART_XML_STRING][1];
            $returnString .= fread($this->_fileHandle, $bufferSize);
            $returnString .= $this->_closingBoundaryLine;
            $this->closeFileHandle();
            $this->_doneReading = true;
            return $returnString;
        }

        // increment internal counters
        $readTo = $this->_bytesRead + $bufferSize;
        $sizeOfCurrentPart = $this->_parts[$this->_currentPart][0];
        $sizeOfNextPart = 0;

        // if we are in a part past the current part, exit
        if ($this->_currentPart > self::PART_CLOSING_XML_STRING) {
            $this->_doneReading = true;
            return;
        }

        // if bytes read is bigger than the current part and we are
        // at the end, return
        if (($this->_bytesRead > $sizeOfCurrentPart) &&
            ($this->_currentPart == self::PART_CLOSING_XML_STRING)) {
                $this->_doneReading = true;
                return;
        }

        // check if we have a next part
        if ($this->_currentPart != self::PART_CLOSING_XML_STRING) {
            $nextPart = $this->_currentPart + 1;
            $sizeOfNextPart = $this->_parts[$nextPart][0];
        }

        $readIntoNextPart = false;
        $readFromRemainingPart = null;
        $readFromNextPart = null;

        // are we crossing into multiple sections of the message in
        // this read?
        if ($readTo > ($sizeOfCurrentPart + $sizeOfNextPart)) {
            if ($this->_currentPart == self::PART_XML_STRING) {
                // If we are in XML string and have crossed over the file
                // return that and whatever we can from the closing boundary
                // string.
                $returnString = $this->_parts[self::PART_XML_STRING][1];
                unset($this->_parts[self::PART_XML_STRING]);
                $returnString .= fread($this->_fileHandle,
                    self::MAX_BUFFER_SIZE);
                $this->closeFileHandle();

                $readFromClosingString = $readTo -
                    ($sizeOfCurrentPart + $sizeOfNextPart);
                $returnString .= substr(
                    $this->_parts[self::PART_CLOSING_XML_STRING][1], 0,
                    $readFromClosingString);
                $this->_bytesRead = $readFromClosingString;
                $this->_currentPart = self::PART_CLOSING_XML_STRING;
                return $returnString;

            } elseif ($this->_currentPart == self::PART_FILE_BINARY) {
                // We have read past the entire message, so return it.
                $returnString .= fread($this->_fileHandle,
                    self::MAX_BUFFER_SIZE);
                $returnString .= $this->_closingBoundaryLine;
                $this->closeFileHandle();
                $this->_doneReading = true;
                return $returnString;
            }
        // are we just crossing from one section into another?
        } elseif ($readTo >= $sizeOfCurrentPart) {
            $readIntoNextPart = true;
            $readFromRemainingPart = $sizeOfCurrentPart - $this->_bytesRead;
            $readFromNextPart = $readTo - $sizeOfCurrentPart;
        }

        if (!$readIntoNextPart) {
            // we are not crossing any section so just return something
            // from the current part
            switch ($this->_currentPart) {
                case self::PART_XML_STRING:
                    $returnString = $this->readFromStringPart(
                        $this->_currentPart, $this->_bytesRead, $bufferSize);
                    break;
                case self::PART_FILE_BINARY:
                    $returnString = fread($this->_fileHandle, $bufferSize);
                    break;
                case self::PART_CLOSING_XML_STRING:
                    $returnString = $this->readFromStringPart(
                        $this->_currentPart, $this->_bytesRead, $bufferSize);
                    break;
            }
        } else {
            // we are crossing from one section to another, so figure out
            // where we are coming from and going to
            switch ($this->_currentPart) {
                case self::PART_XML_STRING:
                    // crossing from string to file
                    $returnString = $this->readFromStringPart(
                        $this->_currentPart, $this->_bytesRead,
                        $readFromRemainingPart);
                    // free up string
                    unset($this->_parts[self::PART_XML_STRING]);
                    $returnString .= $this->smartfread($this->_fileHandle,
                            $readFromNextPart);
                    $this->_bytesRead = $readFromNextPart - 1;
                    break;
                case self::PART_FILE_BINARY:
                    // skipping past file section
                    $returnString = $this->smartfread($this->_fileHandle,
                            $readFromRemainingPart);
                    $this->closeFileHandle();
                    // read closing boundary string
                    $returnString = $this->readFromStringPart(
                        self::PART_CLOSING_XML_STRING, 0, $readFromNextPart);
                    // have we read past the entire closing boundary string?
                    if ($readFromNextPart >=
                        $this->_parts[self::PART_CLOSING_XML_STRING][0]) {
                        $this->_doneReading = true;
                        return $returnString;
                    }

                    // Reset counter appropriately since we are now just
                    // counting how much of the final string is being read.
                    $this->_bytesRead = $readFromNextPart - 1;
                    break;
                case self::PART_CLOSING_XML_STRING:
                    // reading past the end of the closing boundary
                    if ($readFromRemainingPart > 0) {
                        $returnString = $this->readFromStringPart(
                            $this->_currentPart, $this->_bytesRead,
                            $readFromRemainingPart);
                        $this->_doneReading = true;
                    }
                    return $returnString;
            }
            $this->_currentPart++;
        }
        $this->_bytesRead += $bufferSize;
        return $returnString;
    }

    /**
     * Convenience method to shorthand the reading of non-file parts of the
     * message.
     *
     * @param integer $part The part from which to read (supports only 0 or 2).
     * @param integer $start The point at which to read from.
     * @param integer $length How many characters to read.
     * @return string A string of characters corresponding to the requested
     *                section.
     */
    private function readFromStringPart($part, $start, $length)
    {
        return substr($this->_parts[$part][1], $start, $length);
    }

    /**
     * Return the total size of the mime message.
     *
     * @return integer Total size of the message to be sent.
     */
    public function getTotalSize()
    {
        return $this->_totalSize;
    }

    /**
     * Check whether we have data left to read.
     *
     * @return boolean True if there is data remaining in the mime message,
     *                 false, otherwise.
     */
    public function hasData()
    {
        return !($this->_doneReading);
    }

    /**
     * Close the internal file that we are streaming to the socket.
     *
     * @return void
     */
    protected function closeFileHandle()
    {
        if ($this->_fileHandle !== null) {
            fclose($this->_fileHandle);
        }
    }

    /**
     * Return a Content-type header that includes the current boundary string.
     *
     * @return string A valid HTTP Content-Type header.
     */
    public function getContentType()
    {
        return 'multipart/related; boundary="' .
            $this->_boundaryString . '"' . "\r\n";
    }

}
