<?php

namespace Zend\Mvc\ResponseSender;

use Zend\Http\Header\MultipleHeaderInterface;

class PhpEnvironmentResponseSender extends AbstractResponseSender
{

    /**
     * Send HTTP headers
     *
     * @triggers sendHeaders
     * @return PhpEnvironmentResponseSender
     */
    public function sendHeaders()
    {
        $this->getEventManager()->trigger(self::EVENT_SEND_HEADERS, $this);
        $response = $this->getResponse();
        /* @var $response \Zend\Http\PhpEnvironment\Response */

        if ($response->headersSent()) {
            return $this;
        }
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
        echo '1';
        return $this;
    }

    /**
     * Send content
     *
     * @triggers sendContent
     * @return PhpEnvironmentResponseSender
     */
    public function sendContent()
    {
        $this->getEventManager()->trigger(self::EVENT_SEND_CONTENT, $this);
        $response = $this->getResponse();
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        if ($response->contentSent()) {
            return $this;
        }

        echo $response->getContent();
        $response->setContentSent(true);
        return $this;
    }

    /**
     * Send HTTP response
     *
     * @return PhpEnvironmentResponseSender
     */
    public function sendResponse()
    {
        $this->getEventManager()->trigger(self::EVENT_SEND_RESPONSE, $this);
        $this->sendHeaders()
        ->sendHeaders()
             ->sendContent();
        return $this;
    }

}
