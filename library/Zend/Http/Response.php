<?php

namespace Zend\Http;

use Zend\Stdlib\Message,
    Zend\Stdlib\ResponseDescription;

class Response extends Message implements ResponseDescription
{
    const STATUS_CODE_100 = 100;
    const STATUS_CODE_101 = 101;
    const STATUS_CODE_200 = 200;
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_202 = 202;
    const STATUS_CODE_203 = 203;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_205 = 205;
    const STATUS_CODE_206 = 206;
    const STATUS_CODE_300 = 300;
    const STATUS_CODE_301 = 301;
    const STATUS_CODE_302 = 302;
    const STATUS_CODE_303 = 303;
    const STATUS_CODE_304 = 304;
    const STATUS_CODE_305 = 305;
    const STATUS_CODE_306 = 306;
    const STATUS_CODE_307 = 307;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_401 = 401;
    const STATUS_CODE_402 = 402;
    const STATUS_CODE_403 = 403;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_405 = 405;
    const STATUS_CODE_406 = 406;
    const STATUS_CODE_407 = 407;
    const STATUS_CODE_408 = 408;
    const STATUS_CODE_409 = 409;
    const STATUS_CODE_410 = 410;
    const STATUS_CODE_411 = 411;
    const STATUS_CODE_412 = 412;
    const STATUS_CODE_413 = 413;
    const STATUS_CODE_414 = 414;
    const STATUS_CODE_415 = 415;
    const STATUS_CODE_416 = 416;
    const STATUS_CODE_417 = 417;
    const STATUS_CODE_500 = 500;
    const STATUS_CODE_501 = 501;
    const STATUS_CODE_502 = 502;
    const STATUS_CODE_503 = 503;
    const STATUS_CODE_504 = 504;
    const STATUS_CODE_505 = 505;

    const STATUS_CODE_CUSTOM = 0;

    protected static $recommendedReasonPhrases = array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
    );

    protected $statusCode   = 200;
    protected $reasonPhrase = null;

    protected $headers;

    /**
     * Populate object from string
     *
     * @param  string $string
     * @return Response
     */
    public static function fromString($string)
    {
        $segments = preg_split("/\r\n\r\n/", $string, 2);

        $response = new static();
        // "/^(HTTP\/(?<version>\d+(\.\d+)?) (?P<status>\d{3})( (?P<message>.*?)))$/"
        // Populate headers
        //$response ->headers()->fromString($segments[0]);

        // Populate content, if any
        if (2 === count($segments)) {
            $response->setContent($segments[1]);
        } else {
            $response->setContent('');
        }

        return $response;
    }

//    /**
//     * Constructor
//     *
//     * @param  string $content
//     * @param  int $status
//     * @param  null|array|HttpResponseHeaders $headers
//     * @return void
//     */
//    public function __construct($content = '', $status = 200, $headers = null)
//    {
//        $this->setContent($content);
//
//        if ($headers instanceof ResponseHeaders) {
//            $this->setHeaders($headers);
//        } elseif (is_array($headers)) {
//            $httpHeaders = $this->getHeaders();
//            $httpHeaders->addHeaders($headers);
//            $headers = $httpHeaders;
//        } else {
//            $headers = $this->getHeaders();
//        }
//        $headers->setStatusCode($status);
//    }

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
        if ($this->headers === null || is_string($this->headers)) {
            $this->headers = (is_string($this->headers)) ? ResponseHeaders::fromString($this->headers) : new ResponseHeaders();
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
    public function setStatusCode($code, $reasonPhrase = null)
    {
        $const = get_called_class() . '::STATUS_CODE_' . $code;
        if (!is_numeric($code) || !defined($const)) {
            $code = is_scalar($code) ? $code : gettype($code);
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid status code provided: "%s"',
                $code
            ));
        }
        $this->statusCode = $code;
        if (!is_string($reasonPhrase)) {
            // Not a string? Set it to an empty string
            $this->reasonPhrase = '';
        } else {
            // Strip any line-ending characters before storing
            $reasonPhrase = preg_replace("/(\r|\n)/", '', $reasonPhrase);
            $this->reasonPhrase = $reasonPhrase;
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
     * Is the request forbidden due to ACLs?
     *
     * @return bool
     */
    public function isForbidden()
    {
        return (403 == $this->getStatusCode());
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

    /**
     * Create string representation of response
     *
     * @return string
     */
    public function toString()
    {
        $string = $this->renderStatusLine() . "\r\n";
        if ($this->headers) {
            $string .= $this->headers;
        }
        $string .= $this->getContent();
        return $string;
    }



}
