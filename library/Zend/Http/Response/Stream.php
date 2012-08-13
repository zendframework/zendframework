<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Response;

use Zend\Http\Exception;
use Zend\Http\Response;
use Zend\Stdlib\ErrorHandler;

/**
 * Represents an HTTP response message as PHP stream resource
 *
 * @package    Zend_Http
 * @subpackage Response
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
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Set the response stream
     *
     * @param resource $stream
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
     * @param bool $cleanup
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
     * @throws Exception\InvalidArgumentException
     * @throws Exception\OutOfRangeException
     */
    public static function fromStream($responseString, $stream)
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new Exception\InvalidArgumentException('A valid stream is required');
        }

        $headerComplete = false;
        $headersString  = '';

        $responseArray = explode("\n", $responseString);

        while (count($responseArray)) {
            $nextLine        = array_shift($responseArray);
            $headersString  .= $nextLine."\n";
            $nextLineTrimmed = trim($nextLine);
            if ($nextLineTrimmed == '') {
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

        /** @var Stream $response  */
        $response = static::fromString($headersString);

        if (is_resource($stream)) {
            $response->setStream($stream);
        }

        if (count($responseArray)) {
            $response->content = implode("\n", $responseArray);
        }

        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            if ($header instanceof \Zend\Http\Header\ContentLength) {
                $response->contentLength = (int) $header->getFieldValue();
                if (strlen($response->content) > $response->contentLength) {
                    throw new Exception\OutOfRangeException(sprintf(
                        'Too much content was extracted from the stream (%d instead of %d bytes)',
                        strlen($response->content),
                        $response->contentLength
                    ));
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
     * If you want to get the raw body (as transferred on wire) use
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
     * Get the raw response body (as transferred "on wire") as string
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
            ErrorHandler::start(E_WARNING);
            unlink($this->streamName);
            ErrorHandler::stop();
        }
    }
}
