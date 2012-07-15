<?php

namespace Zend\Console;

use Zend\Stdlib\Message;
use Zend\Stdlib\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    protected $contentSent = false;

    public function contentSent()
    {
        return $this->contentSent;
    }

    /**
     * Set the error level that will be returned to shell.
     *
     * @param integer   $errorLevel
     * @return Response
     */
    public function setErrorLevel($errorLevel){
        $this->setMetadata('errorLevel', $errorLevel);
        return $this;
    }

    /**
     * Get response error level that will be returned to shell.
     *
     * @return integer|0
     */
    public function getErrorLevel(){
        return $this->getMetadata('errorLevel', 0);
    }

    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }
        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    public function send()
    {
        $this->sendContent();
        $errorLevel = (int)$this->getMetadata('errorLevel',0);
        exit($errorLevel);
    }
}
