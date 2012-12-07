<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Console\Response;

class ConsoleResponseSender implements ResponseSenderInterface
{
    /**
     * Send content
     *
     * @param SendResponseEvent $event
     * @return ConsoleResponseSender
     */
    public function sendContent(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        echo $response->getContent();
        $event->setContentSent();
        return $this;
    }

    /**
     * Send the response
     *
     * @param SendResponseEvent $event
     * @return void
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response instanceof Response) {
            $this->sendContent($response);
            $errorLevel = (int) $response->getMetadata('errorLevel',0);
            $event->stopPropagation(true);
            exit($errorLevel);
        }
    }

}
