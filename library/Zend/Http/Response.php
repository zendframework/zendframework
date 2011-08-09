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
            $headers = $this->headers();
        }

        $this->setStatusCode($status);
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

        if ($code === null) {
            return $messages;
        } elseif (isset($messages[$code])) {
            return $messages[$code];
        } else {
            return 'Unknown';
        }
    }

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
