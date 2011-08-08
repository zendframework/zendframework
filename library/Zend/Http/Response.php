<?php

namespace Zend\Http;

use Zend\Stdlib\Message,
    Zend\Stdlib\ResponseDescription;

class Response extends Message implements ResponseDescription
{

    const PATTERN_STATUS_LINE = "/^(HTTP\/(?<version>\d+(\.\d+)?) (?P<status>\d{3})( (?P<message>.*?)))$/";

    protected $allowedStatusCodes = array(
        100, 101,
        200, 201, 202, 203, 204, 205, 206,
        300, 301, 302, 303, 304, 305, 306, 307,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417,
        500, 501, 502, 503, 504, 505,
    );

    /**
     * @var array Status codes indicating empty response
     */
    protected $emptyCodes = array(201, 204, 304);

    protected $statusCode      = 200;
    protected $statusMessage   = 'OK';

    protected $headers;

    /**
     * Constructor
     * 
     * @param  string $content 
     * @param  int $status 
     * @param  null|array|HttpResponseHeaders $headers 
     * @return void
     */
    public function __construct($content = '', $status = 200, $headers = null)
    {
        $this->setContent($content);

        if ($headers instanceof ResponseHeaders) {
            $this->setHeaders($headers);
        } elseif (is_array($headers)) {
            $httpHeaders = $this->getHeaders();
            $httpHeaders->addHeaders($headers);
            $headers = $httpHeaders;
        } else {
            $headers = $this->getHeaders();
        }
        $headers->setStatusCode($status);
    }

    /**
     * Render the status line header
     *
     * @return string
     */
    public function renderStatusLine()
    {
        $status = sprintf(
            'HTTP/%s %d %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getStatusMessage()
        );
        return trim($status);
    }



    /**
     * Set response headers
     *
     * @param  \Zend\Http\ResponseHeaders $headers
     * @return \Zend\Http\Response
     */
    public function setHeaders(ResponseHeaders $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get response headers
     * 
     * @return \Zend\Http\ResponseHeaders
     */
    public function headers()
    {
        if ($this->headers === null) {
            $this->headers = new ResponseHeaders();
        }
        return $this->headers;
    }

    /**
     * Retrieve HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get HTTP status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Set HTTP status code and (optionally) message
     *
     * @param  string|float $code
     * @param  null|string $text
     * @return Headers
     */
    public function setStatusCode($code, $text = null)
    {
        if (!is_numeric($code) || !in_array($code, $this->allowedStatusCodes)) {
            $code = is_scalar($code) ? $code : gettype($code);
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid status code provided: "%s"',
                $code
            ));
        }
        $this->statusCode = $code;
        if (!is_string($text)) {
            // Not a string? Set it to an empty string
            $this->statusMessage = '';
        } else {
            // Strip any lineending characters before storing
            $text = preg_replace("/(\r|\n)/", '', $text);
            $this->statusMessage = $text;
        }
        return $this;
    }

    /**
     * Does the status code indicate a client error?
     *
     * @return bool
     */
    public function isClientError()
    {
        $code = $this->getStatusCode();
        return ($code < 500 && $code >= 400);
    }

    /**
     * Does the status code indicate an empty response?
     *
     * @return bool
     */
    public function isEmpty()
    {
        return in_array($this->getStatusCode(), $this->emptyCodes);
    }

    /**
     * Is the request forbidden due to ACLs?
     *
     * @return bool
     */
    public function isForbidden()
    {
        return (403 == $this->getStatusCode());
    }

    public function isFresh()
    {
    }

    /**
     * Is the current status "informational"?
     *
     * @return bool
     */
    public function isInformational()
    {
        $code = $this->getStatusCode();
        return ($code >= 100 && $code < 200);
    }

    /**
     * Does the status code indicate the resource is not found?
     *
     * @return bool
     */
    public function isNotFound()
    {
        return (404 === $this->getStatusCode());
    }

    public function isNotModified(HttpRequest $request)
    {
    }

    /**
     * Do we have a normal, OK response?
     *
     * @return bool
     */
    public function isOk()
    {
        return (200 === $this->getStatusCode());
    }

    /**
     * Does the status code reflect a server error?
     *
     * @return bool
     */
    public function isServerError()
    {
        $code = $this->getStatusCode();
        return (500 <= $code && 600 > $code);
    }

    /**
     * Was the response successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $code = $this->getStatusCode();
        return (200 <= $code && 300 > $code);
    }

//    /**
//     * Create string representation of response
//     *
//     * @return string
//     */
//    public function __toString()
//    {
//        return $this->getHeaders() . "\r\n" . $this->getContent();
//    }
//
//    /**
//     * Populate object from string
//     *
//     * @param  string $string
//     * @return Response
//     */
//    public function fromString($string)
//    {
//        $segments = preg_split("/\r\n\r\n/", $string, 2);
//
//        // Populate headers
//        $this->headers()->fromString($segments[0]);
//
//        // Populate content, if any
//        if (2 === count($segments)) {
//            $this->setContent($segments[1]);
//        } else {
//            $this->setContent('');
//        }
//
//        return $this;
//    }

}
