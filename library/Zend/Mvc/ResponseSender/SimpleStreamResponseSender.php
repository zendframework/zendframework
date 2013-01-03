<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Http\Response\Stream;

class SimpleStreamResponseSender implements ResponseSenderInterface
{

    /**
     * Send headers
     *
     * @param SendResponseEvent $event
     */
    public function sendHeaders(SendResponseEvent $event)
    {
        if ($event->headersSent()) {
            return $this;
        }
        $response = $event->getResponse();
        /* @var $response Stream */
        $status  = $response->renderStatusLine();
        header($status);
        /* @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }
        $event->setHeadersSent();
        return $this;
    }

    /**
     * Send the stream
     *
     * @param SendResponseEvent $event
     * @return SimpleStreamResponseSender
     */
    public function sendStream(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        /* @var $response Stream */

        $stream = $response->getStream();
        fpassthru($stream);
        $event->setContentSent();
    }

    /**
     * Send stream response
     *
     * @param SendResponseEvent $event
     * @return SimpleStreamResponseSender
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getParam('response');
        if ($response instanceof Stream) {
            $this->sendHeaders($event)
                ->sendStream($event);
            $event->stopPropagation(true);
        }
        return $this;
    }

}
