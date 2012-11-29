<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Stdlib\ResponseInterface;

abstract class AbstractResponseSender implements ResponseSenderInterface
{

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $headersSent = false;

    /**
     * @var bool
     */
    protected $contentSent = false;

    /**
     * Get response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function headersSent()
    {
        return $this->headersSent;
    }

    /**
     * @return bool
     */
    public function contentSent()
    {
        return $this->contentSent;
    }

}
