<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Mvc\ResponseSender\SendResponseEvent;;

interface ResponseSenderInterface
{
    /**
     * Send the response
     *
     * @param SendResponseEvent $event
     * @return void
     */
    public function __invoke(SendResponseEvent $event);

}
