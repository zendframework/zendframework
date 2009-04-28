<?php

interface Zend_Tool_Framework_Manifest_Interface
{
    
    /**
     * The following methods are completely optional, and any combination of them
     * can be used as part of a manifest.  The manifest repository will process
     * the return values of these actions as specfied in the following method docblocks.
     * 
     * Since these actions are
     * 
     */
    
    /**
     * getMetadata()
     * 
     * Should either return a single metadata object or an array
     * of metadata objects
     * 
     * @return array|Zend_Tool_Framework_Manifest_Metadata
     **

    public function getMetadata();

     **/
    
    
    
    /**
     * getActions()
     * 
     * Should either return a single action, or an array
     * of actions
     * 
     * @return array|Zend_Tool_Framework_Action_Interface
     **
    
    public function getActions();

     **/
    
    
    
    /**
     * getProviders()
     * 
     * Should either return a single provider or an array
     * of providers
     * 
     **
    
    public function getProviders();

     **/
    
}
