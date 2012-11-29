<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Http\Header\MultipleHeaderInterface;

class HttpResponseSender extends AbstractResponseSender
{
    /**
     * Send HTTP headers
     *
     * @return HttpResponseSender
     */
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return $this;
        }

        $response = $this->getResponse();
        /* @var $response \Zend\Http\Response */
        $status  = $response->renderStatusLine();
        header($status);

        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $this->headersSent = true;
        return $this;
    }

    /**
     * Send content
     *
     * @return HttpResponseSender
     */
    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }

        $response = $this->getResponse();
        echo $response->getContent();
        $this->contentSent = true;
        return $this;
    }

    /**
     * Send HTTP response
     *
     * @return HttpResponseSender
     */
    public function sendResponse()
    {
        $this->sendHeaders()
             ->sendContent();
        return $this;
    }

}
