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
class MimeBodyString
{

    /**
     * The source string.
     *
     * @var string
     */
    protected $_sourceString = '';

    /**
     * The size of the MIME message.
     * @var integer
     */
    protected $_bytesRead = 0;

    /**
     * Create a new MimeBodyString object.
     *
     * @param string $sourceString The string we are wrapping.
     */
    public function __construct($sourceString)
    {
        $this->_sourceString = $sourceString;
        $this->_bytesRead = 0;
    }

    /**
     * Read the next chunk of the string.
     *
     * @param integer $bytesRequested The size of the chunk that is to be read.
     * @return string A corresponding piece of the string.
     */
    public function read($bytesRequested)
    {
      $len = strlen($this->_sourceString);
      if($this->_bytesRead == $len) {
          return FALSE;
      } else if($bytesRequested > $len - $this->_bytesRead) {
          $bytesRequested = $len - $this->_bytesRead;
      }

      $buffer = substr($this->_sourceString, $this->_bytesRead, $bytesRequested);
      $this->_bytesRead += $bytesRequested;

      return $buffer;
    }

    /**
     * The length of the string.
     *
     * @return int The length of the string contained in the object.
     */
    public function getSize()
    {
      return strlen($this->_sourceString);
    }


}
