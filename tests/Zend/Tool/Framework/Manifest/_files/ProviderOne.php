<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Framework/Registry/EnabledInterface.php';

class Zend_Tool_Framework_Manifest_ProviderOne 
    implements Zend_Tool_Framework_Provider_Interface, Zend_Tool_Framework_Registry_EnabledInterface
{
    
    protected $_registry = null;
    
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
    }
    
}