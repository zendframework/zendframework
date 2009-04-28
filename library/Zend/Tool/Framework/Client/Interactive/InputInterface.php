<?php

interface Zend_Tool_Framework_Client_Interactive_InputInterface
{
    
    /**
     * Handle Interactive Input Request
     *
     * @param Zend_Tool_Framework_Client_Interactive_InputRequest $inputRequest
     * @return Zend_Tool_Framework_Client_Interactive_InputResponse|string
     */
    public function handleInteractiveInputRequest(Zend_Tool_Framework_Client_Interactive_InputRequest $inputRequest);
    
    public function getMissingParameterPromptString(Zend_Tool_Framework_Provider_Interface $provider, Zend_Tool_Framework_Action_Interface $actionInterface, $missingParameterName);
    
}