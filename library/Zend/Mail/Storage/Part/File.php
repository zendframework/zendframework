<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Storage\Part;

use Zend\Mail\Headers;
use Zend\Mail\Storage\Part;
use Zend\Mime;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Storage
 */
class File extends Part
{
    protected $_contentPos = array();
    protected $_partPos = array();
    protected $_fh;

    /**
     * Public constructor
     *
     * This handler supports the following params:
     * - file     filename or open file handler with message content (required)
     * - startPos start position of message or part in file (default: current position)
     * - endPos   end position of message or part in file (default: end of file)
     *
     * @param   array $params  full message with or without headers
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $params)
    {
        if (empty($params['file'])) {
            throw new Exception\InvalidArgumentException('no file given in params');
        }

        if (!is_resource($params['file'])) {
            $this->_fh = fopen($params['file'], 'r');
        } else {
            $this->_fh = $params['file'];
        }
        if (!$this->_fh) {
            throw new Exception\RuntimeException('could not open file');
        }
        if (isset($params['startPos'])) {
            fseek($this->_fh, $params['startPos']);
        }
        $header = '';
        $endPos = isset($params['endPos']) ? $params['endPos'] : null;
        while (($endPos === null || ftell($this->_fh) < $endPos) && trim($line = fgets($this->_fh))) {
            $header .= $line;
        }

        $this->_headers = Headers::fromString($header);

        $this->_contentPos[0] = ftell($this->_fh);
        if ($endPos !== null) {
            $this->_contentPos[1] = $endPos;
        } else {
            fseek($this->_fh, 0, SEEK_END);
            $this->_contentPos[1] = ftell($this->_fh);
        }
        if (!$this->isMultipart()) {
            return;
        }

        $boundary = $this->getHeaderField('content-type', 'boundary');
        if (!$boundary) {
            throw new Exception\RuntimeException('no boundary found in content type to split message');
        }

        $part = array();
        $pos = $this->_contentPos[0];
        fseek($this->_fh, $pos);
        while (!feof($this->_fh) && ($endPos === null || $pos < $endPos)) {
            $line = fgets($this->_fh);
            if ($line === false) {
                if (feof($this->_fh)) {
                    break;
                }
                throw new Exception\RuntimeException('error reading file');
            }

            $lastPos = $pos;
            $pos = ftell($this->_fh);
            $line = trim($line);

            if ($line == '--' . $boundary) {
                if ($part) {
                    // not first part
                    $part[1] = $lastPos;
                    $this->_partPos[] = $part;
                }
                $part = array($pos);
            } elseif ($line == '--' . $boundary . '--') {
                $part[1] = $lastPos;
                $this->_partPos[] = $part;
                break;
            }
        }
        $this->_countParts = count($this->_partPos);

    }


    /**
     * Body of part
     *
     * If part is multipart the raw content of this part with all sub parts is returned
     *
     * @param resource $stream Optional
     * @return string body
     */
    public function getContent($stream = null)
    {
        fseek($this->_fh, $this->_contentPos[0]);
        if ($stream !== null) {
            return stream_copy_to_stream($this->_fh, $stream, $this->_contentPos[1] - $this->_contentPos[0]);
        }
        $length = $this->_contentPos[1] - $this->_contentPos[0];
        return $length < 1 ? '' : fread($this->_fh, $length);
    }

    /**
     * Return size of part
     *
     * Quite simple implemented currently (not decoding). Handle with care.
     *
     * @return int size
     */
    public function getSize()
    {
        return $this->_contentPos[1] - $this->_contentPos[0];
    }

    /**
     * Get part of multipart message
     *
     * @param  int $num number of part starting with 1 for first part
     * @throws Exception\RuntimeException
     * @return Part wanted part
     */
    public function getPart($num)
    {
        --$num;
        if (!isset($this->_partPos[$num])) {
            throw new Exception\RuntimeException('part not found');
        }

        return new self(array('file' => $this->_fh, 'startPos' => $this->_partPos[$num][0],
                              'endPos' => $this->_partPos[$num][1]));
    }
}
