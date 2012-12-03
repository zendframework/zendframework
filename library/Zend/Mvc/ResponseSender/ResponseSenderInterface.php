<?php

namespace Zend\Mvc\ResponseSender;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\Stdlib\ResponseInterface;

interface ResponseSenderInterface extends EventManagerAwareInterface
{
    /**
     * Get response
     *
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * Set response
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function setResponse(ResponseInterface $response);

    /**
     * Send the response
     *
     * @return void
     */
    public function sendResponse();

}
