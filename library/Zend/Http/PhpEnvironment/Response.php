<?php


namespace Zend\Http\PhpEnvironment;

use Zend\Http\Header\MultipleHeaderInterface,
    Zend\Http\Response as HttpResponse,
    Zend\Stdlib\Parameters;

class Response extends HttpResponse
{
    protected $headersSent = false;

    protected $contentSent = false;

    public function __construct()
    {
    }
    
    public function headersSent()
    {
        return $this->headersSent;
    }
    
    public function contentSent()
    {
        return $this->contentSent;
    }
    
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return $this;
        }

        $status  = $this->renderStatusLine();
        header($status);

        foreach ($this->headers() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $this->headersSent = true;
        return $this;
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
        $this->sendHeaders()
             ->sendContent();
        return $this;
    }
    
}
    
