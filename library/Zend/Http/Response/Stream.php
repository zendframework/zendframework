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

use Zend\Http\Response,
    Zend\Http\Exception;


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
     * The Content-Length value, if set
     *
     * @var int
     */
    protected $contentLength = null;

    /**
     * The portion of the body that has alredy been streamed
     *
     * @var int
     */
    protected $contentStreamed = 0;

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
    protected $streamName;

    /**
     * Should we clean up the stream file when this response is closed?
     *
     * @var boolean
     */
    protected $cleanup;

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
        return $this->cleanup;
    }

    /**
     * Set the cleanup trigger
     *
     * @param $cleanup Set cleanup trigger
     */
    public function setCleanup($cleanup = true)
    {
        $this->cleanup = $cleanup;
    }

    /**
     * Get file name associated with the stream
     *
     * @return string
     */
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * Set file name associated with the stream
     *
     * @param string $streamName Name to set
     * @return Stream
     */
    public function setStreamName($streamName)
    {
        $this->streamName = $streamName;
        return $this;
    }


    /**
     * Create a new Zend\Http\Response\Stream object from a stream
     *
     * @param  string $responseString
     * @param  resource $stream
     * @return Stream
     */
    public static function fromStream($responseString, $stream)
    {

        if (!is_resource($stream)) {
            throw new Exception\InvalidArgumentException('A valid stream is required');
        }

        $headerComplete = false;
        $headersString  = '';

        $responseArray = explode("\n",$responseString);

        while (count($responseArray)) {
            $nextLine = array_shift($responseArray);
            $headersString .= $nextLine."\n";
            $nextLineTrimmed = trim($nextLine);
            if ($nextLineTrimmed == "") {
                $headerComplete = true;
                break;
            }

        }

        if (!$headerComplete) {
            while (false !== ($nextLine = fgets($stream))) {

                $headersString .= trim($nextLine)."\r\n";
                if ($nextLine == "\r\n" || $nextLine == "\n") {
                    $headerComplete = true;
                    break;
                }
            }
        }

        if (!$headerComplete) {
            throw new Exception\OutOfRangeException('End of header not found');
        }

        $response = static::fromString($headersString);

        if (is_resource($stream)) {
            $response->setStream($stream);
        }

        if (count($responseArray)) {
            $response->content = implode("\n", $responseArray);
        }

        $headers = $response->getHeaders();
        foreach($headers as $header) {
            if ($header instanceof \Zend\Http\Header\ContentLength) {
                $response->contentLength = (int) $header->getFieldValue();
                if (strlen($response->content) > $response->contentLength) {
                    throw new Exception\OutOfRangeException(
                        sprintf('Too much content was extracted from the stream (%d instead of %d bytes)',
                                    strlen($this->content), $this->contentLength));
                }
                break;
            }
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
        return $this->content;
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
        if (!is_null($this->contentLength)) {
            $bytes =  $this->contentLength - $this->contentStreamed;
        } else {
            $bytes = -1; //Read the whole buffer
        }

        if (!is_resource($this->stream) || $bytes == 0) {
            return '';
        }

        $this->content         .= stream_get_contents($this->stream, $bytes);
        $this->contentStreamed += strlen($this->content);

        if ($this->contentLength == $this->contentStreamed) {
            $this->stream = null;
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (is_resource($this->stream)) {
            $this->stream = null; //Could be listened by others
        }
        if ($this->cleanup) {
            @unlink($this->stream_name);
        }
    }
}
