<?php

namespace Zend\Mvc;

use Zend\Http\Response as HttpResponse,
    Zend\Stdlib\ResponseDescription as Response;

/**
 * A response that can send itself
 */
class SendableResponse
{
    protected $response;

    /**
     * Constructor
     * 
     * @param  Response $response 
     * @return void
     */
    public function __construct(Response $response = null)
    {
        if ($response) {
            $this->setResponse($response);
        }
    }

    /**
     * Set the response object being decorated
     * 
     * @param  Response $response 
     * @return SendableResponse
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get the response object being decorated
     * 
     * @return void
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Send the response
     *
     * Echos response content, and, if an HTTP response, initially sends 
     * headers.
     * 
     * @return void
     */
    public function send()
    {
        if ($this->response instanceof HttpResponse) {
            $this->sendHttpResponse();
        }

        echo $this->response->getContent();
    }

    /**
     * Send an HTTP response
     *
     * Sends HTTP headers
     * 
     * @return void
     */
    protected function sendHttpResponse()
    {
        if (headers_sent()) {
            return;
        }

        $version = $this->response->getVersion();
        $code    = $this->response->getStatusCode();
        $message = $this->response->getReasonPhrase();
        $status  = sprintf('HTTP/%s %d %s', $version, $code, $message);
        header($status);

        foreach ($this->response->headers() as $header) {
            header($header->toString());
        }
    }
}
