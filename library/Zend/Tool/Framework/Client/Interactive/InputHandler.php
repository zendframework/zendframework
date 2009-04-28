<?php

class Zend_Tool_Framework_Client_Interactive_InputHandler
{
    
    /**
     * @var Zend_Tool_Framework_Client_Interactive_InputInterface
     */
    protected $_client = null;
    
    protected $_inputRequest = null;
    
    public function setClient(Zend_Tool_Framework_Client_Interactive_InputInterface $client)
    {
        $this->_client = $client;
        return $this;
    }
    
    public function setInputRequest($inputRequest)
    {
        if (is_string($inputRequest)) {
            require_once 'Zend/Tool/Framework/Client/Interactive/InputRequest.php';
            $inputRequest = new Zend_Tool_Framework_Client_Interactive_InputRequest($inputRequest);
        } elseif (!$inputRequest instanceof Zend_Tool_Framework_Client_Interactive_InputRequest) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('promptInteractive() requires either a string or an instance of Zend_Tool_Framework_Client_Interactive_InputRequest.');
        }
        
        $this->_inputRequest = $inputRequest;
        return $this;
    }
    
    public function handle()
    {
        $inputResponse = $this->_client->handleInteractiveInputRequest($this->_inputRequest);
        
        if (is_string($inputResponse)) {
            require_once 'Zend/Tool/Framework/Client/Interactive/InputResponse.php';
            $inputResponse = new Zend_Tool_Framework_Client_Interactive_InputResponse($inputResponse); 
        } elseif (!$inputResponse instanceof Zend_Tool_Framework_Client_Interactive_InputResponse) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('The registered $_interactiveCallback for the client must either return a string or an instance of Zend_Tool_Framework_Client_Interactive_InputResponse.');
        }
        
        return $inputResponse;
    }
    
    
}