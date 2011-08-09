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
            $headers = $this->getHeaders();
        }
        $headers->setStatusCode($status);
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
     * Do we have a redirect?
     * 
     * @return bool 
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        return (300 <= $code && 400 > $code);
    }
    
    /**
     * Was the response successful?
     *
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return (200 <= $code && 300 > $code);
    }
/**
* A convenience function that returns a text representation of
* HTTP response codes. Returns 'Unknown' for unknown codes.
* Returns array of all codes, if $code is not specified.
*
* Conforms to HTTP/1.1 as defined in RFC 2616 (except for 'Unknown')
* See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10 for reference
*
* @param int $code HTTP response code
* @param boolean $http11 Use HTTP version 1.1
* @return string
*/
    public static function responseCodeAsText($code = null, $http11 = true)
    {
        $messages = self::$messages;
        if (! $http11) $messages[302] = 'Moved Temporarily';

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

    /**
* Extract the response code from a response string
*
* @param string $response_str
* @return int
*/
    public static function extractCode($response_str)
    {
        preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response_str, $m);

        if (isset($m[1])) {
            return (int) $m[1];
        } else {
            return false;
        }
    }

    /**
* Extract the HTTP message from a response
*
* @param string $response_str
* @return string
*/
    public static function extractMessage($response_str)
    {
        preg_match("|^HTTP/[\d\.x]+ \d+ ([^\r\n]+)|", $response_str, $m);

        if (isset($m[1])) {
            return $m[1];
        } else {
            return false;
        }
    }

    /**
* Extract the HTTP version from a response
*
* @param string $response_str
* @return string
*/
    public static function extractVersion($response_str)
    {
        preg_match("|^HTTP/([\d\.x]+) \d+|", $response_str, $m);

        if (isset($m[1])) {
            return $m[1];
        } else {
            return false;
        }
    }

    /**
* Extract the headers from a response string
*
* @param string $response_str
* @return array
*/
    public static function extractHeaders($response_str)
    {
        $headers = array();

        // First, split body and headers
        $parts = preg_split('|(?:\r?\n){2}|m', $response_str, 2);
        if (! $parts[0]) return $headers;

        // Split headers part to lines
        $lines = explode("\n", $parts[0]);
        unset($parts);
        $last_header = null;

        foreach($lines as $line) {
            $line = trim($line, "\r\n");
            if ($line == "") break;

            // Locate headers like 'Location: ...' and 'Location:...' (note the missing space)
            if (preg_match("|^([\w-]+):\s*(.+)|", $line, $m)) {
                unset($last_header);
                $h_name = strtolower($m[1]);
                $h_value = $m[2];

                if (isset($headers[$h_name])) {
                    if (! is_array($headers[$h_name])) {
                        $headers[$h_name] = array($headers[$h_name]);
                    }

                    $headers[$h_name][] = $h_value;
                } else {
                    $headers[$h_name] = $h_value;
                }
                $last_header = $h_name;
            } elseif (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                if (is_array($headers[$last_header])) {
                    end($headers[$last_header]);
                    $last_header_key = key($headers[$last_header]);
                    $headers[$last_header][$last_header_key] .= $m[1];
                } else {
                    $headers[$last_header] .= $m[1];
                }
            }
        }

        return $headers;
    }

    /**
* Extract the body from a response string
*
* @param string $response_str
* @return string
*/
    public static function extractBody($response_str)
    {
        $parts = preg_split('|(?:\r?\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return $parts[1];
        }
        return '';
    }

    /**
* Decode a "chunked" transfer-encoded body and return the decoded text
*
* @param string $body
* @return string
*/
    public static function decodeChunkedBody($body)
    {
        $decBody = '';

        // If mbstring overloads substr and strlen functions, we have to
        // override it's internal encoding
        if (function_exists('mb_internal_encoding') &&
           ((int) ini_get('mbstring.func_overload')) & 2) {

            $mbIntEnc = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }

        while (trim($body)) {
            if (! preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $body, $m)) {
                throw new Exception\RuntimeException("Error parsing body - doesn't seem to be a chunked message");
            }

            $length = hexdec(trim($m[1]));
            $cut = strlen($m[0]);
            $decBody .= substr($body, $cut, $length);
            $body = substr($body, $cut + $length + 2);
        }

        if (isset($mbIntEnc)) {
            mb_internal_encoding($mbIntEnc);
        }

        return $decBody;
    }

    /**
* Decode a gzip encoded message (when Content-encoding = gzip)
*
* Currently requires PHP with zlib support
*
* @param string $body
* @return string
*/
    public static function decodeGzip($body)
    {
        if (! function_exists('gzinflate')) {
            throw new Exception\RuntimeException(
                'zlib extension is required in order to decode "gzip" encoding'
            );
        }

        return gzinflate(substr($body, 10));
    }

    /**
* Decode a zlib deflated message (when Content-encoding = deflate)
*
* Currently requires PHP with zlib support
*
* @param string $body
* @return string
*/
    public static function decodeDeflate($body)
    {
        if (! function_exists('gzuncompress')) {
            throw new Exception\RuntimeException(
                'zlib extension is required in order to decode "deflate" encoding'
            );
        }

        /**
* Some servers (IIS ?) send a broken deflate response, without the
* RFC-required zlib header.
*
* We try to detect the zlib header, and if it does not exsit we
* teat the body is plain DEFLATE content.
*
* This method was adapted from PEAR HTTP_Request2 by (c) Alexey Borzov
*
* @link http://framework.zend.com/issues/browse/ZF-6040
*/
        $zlibHeader = unpack('n', substr($body, 0, 2));
        if ($zlibHeader[1] % 31 == 0) {
            return gzuncompress($body);
        } else {
            return gzinflate($body);
        }
    }

    /**
     * Create a new Zend\Http\Response object from a string
     *
     * @param string $response_str
     * @return \Zend\Http\Response
     */
    public static function fromString($response_str)
    {
        return new Response($response_str);
    }
}
