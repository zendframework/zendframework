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
 * @package    Zend_Http
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Http\Response;

use Zend\Http\Response;

/**
 * Zend_Http_Response represents an HTTP 1.0 / 1.1 response message. It
 * includes easy access to all the response's different elemts, as well as some
 * convenience methods for parsing and validating HTTP responses.
 *
 * @package    Zend_Http
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Stream extends Response
{
    /**
     * Response as stream
     *
     * @var resource
     */
    protected $stream;

    /**
     * The name of the file containing the stream
     *
     * Will be empty if stream is not file-based.
     *
     * @var string
     */
    protected $stream_name;

    /**
     * Should we clean up the stream file when this response is closed?
     *
     * @var boolean
     */
    protected $_cleanup;

    /**
     * Get the response as stream
     *
     * @return resourse
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Set the response stream
     *
     * @param resourse $stream
     * @return Stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Get the cleanup trigger
     *
     * @return boolean
     */
    public function getCleanup()
    {
        return $this->_cleanup;
    }

    /**
     * Set the cleanup trigger
     *
     * @param $cleanup Set cleanup trigger
     */
    public function setCleanup($cleanup = true)
    {
        $this->_cleanup = $cleanup;
    }

    /**
     * Get file name associated with the stream
     *
     * @return string
     */
    public function getStreamName()
    {
        return $this->stream_name;
    }

    /**
     * Set file name associated with the stream
     *
     * @param string $stream_name Name to set
     * @return Stream
     */
    public function setStreamName($stream_name)
    {
        $this->stream_name = $stream_name;
        return $this;
    }

    /**
     * Create a new Zend\Http\Response\Stream object from a string
     *
     * @param  string $response_str
     * @param  resource $stream
     * @return Stream
     */
    public static function fromStream($response_str, $stream)
    {
        $response= new static();

        $response::fromString($response_str);
        if (is_resource($stream)) {
            $response->setStream($stream);
        }

        return $response;
    }

    /**
     * Get the response body as string
     *
     * This method returns the body of the HTTP response (the content), as it
     * should be in it's readable version - that is, after decoding it (if it
     * was decoded), deflating it (if it was gzip compressed), etc.
     *
     * If you want to get the raw body (as transfered on wire) use
     * $this->getRawBody() instead.
     *
     * @return string
     */
    public function getBody()
    {
        if ($this->stream != null) {
            $this->readStream();
        }
        return parent::getBody();
    }

    /**
     * Get the raw response body (as transfered "on wire") as string
     *
     * If the body is encoded (with Transfer-Encoding, not content-encoding -
     * IE "chunked" body), gzip compressed, etc. it will not be decoded.
     *
     * @return string
     */
    public function getRawBody()
    {
        if ($this->stream) {
            $this->readStream();
        }
        return $this->body;
    }

    /**
     * Read stream content and return it as string
     *
     * Function reads the remainder of the body from the stream and closes the stream.
     *
     * @return string
     */
    protected function readStream()
    {
        if (!is_resource($this->stream)) {
            return '';
        }

        $this->body = stream_get_contents($this->stream);
        fclose($this->stream);
        $this->stream = null;
    }

    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
            $this->stream = null;
        }
        if ($this->_cleanup) {
            @unlink($this->stream_name);
        }
    }

}
