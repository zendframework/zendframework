<?php

namespace Zend\Http;

use Zend\Stdlib\Message,
    Zend\Stdlib\ResponseDescription;

class Response extends Message implements ResponseDescription
{
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
     * Create string representation of response
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getHeaders() . "\r\n" . $this->getContent();
    }

    /**
     * Populate object from string
     * 
     * @param  string $string 
     * @return Response
     */
    public function fromString($string)
    {
        $segments = preg_split("/\r\n\r\n/", $string, 2);

        // Populate headers
        $this->getHeaders()->fromString($segments[0]);

        // Populate content, if any
        if (2 === count($segments)) {
            $this->setContent($segments[1]);
        } else {
            $this->setContent('');
        }

        return $this;
    }


    /**
     * Get response headers
     * 
     * @return ResponseHeaders
     */
    public function getHeaders()
    {
        if (null === $this->headers) {
            $this->setHeaders(new ResponseHeaders());
        }
        return $this->headers;
    }

    /**
     * Set response headers
     * 
     * @param  HttpResponseHeaders $headers 
     * @return Response
     */
    public function setHeaders(ResponseHeaders $headers)
    {
        $this->headers = $headers;
        return $this;
    }
}
