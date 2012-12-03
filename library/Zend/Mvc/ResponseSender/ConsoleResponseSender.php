<?php

namespace Zend\Mvc\ResponseSender;

class ConsoleResponseSender extends AbstractResponseSender
{
    /**
     * Send content
     *
     * @return ConsoleResponseSender
     */
    public function sendContent()
    {
        $this->getEventManager()->trigger(self::EVENT_SEND_CONTENT, $this);
        $response = $this->getResponse();
        /* @var $response \Zend\Console\Response */
        if ($response->contentSent()) {
            return $this;
        }
        echo $response->getContent();
        $response->setContentSent(true);
        return $this;
    }

    /**
     * Send the response
     *
     * @return void
     */
    public function sendResponse()
    {
        $this->getEventManager()->trigger(self::EVENT_SEND_RESPONSE, $this);
        $this->sendContent();
        $response = $this->getResponse();
        $errorLevel = (int)$response->getMetadata('errorLevel',0);
        exit($errorLevel);
    }

}
