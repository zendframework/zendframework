<?php

class Zend_Tool_Framework_Client_Interactive_InputRequest
{
    protected $_content = null;
    
    public function __construct($content = null)
    {
        if ($content) {
            $this->setContent($content);
        }
    }
    
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }
    
    public function getContent()
    {
        return $this->_content;
    }

    public function __toString()
    {
        return $this->_content;
    }
}