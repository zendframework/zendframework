<?php

require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/Indexable.php';

class Zend_Tool_Framework_Manifest_ManifestBadProvider 
    implements Zend_Tool_Framework_Manifest_ProviderManifestable,
        Zend_Tool_Framework_Manifest_Indexable
{
    
    public function getIndex()
    {
        return 20;
    }
    
    public function getProviders()
    {
        return new ArrayObject();
    }
    
}
