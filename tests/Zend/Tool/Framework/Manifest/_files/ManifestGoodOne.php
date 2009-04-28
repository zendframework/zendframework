<?php

require_once 'Zend/Tool/Framework/Manifest/ActionManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/MetadataManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/Indexable.php';
require_once 'Zend/Tool/Framework/Metadata/Basic.php';

require_once 'ProviderOne.php';
require_once 'ActionOne.php';

class Zend_Tool_Framework_Manifest_ManifestGoodOne 
    implements Zend_Tool_Framework_Manifest_ActionManifestable, 
        Zend_Tool_Framework_Manifest_ProviderManifestable,
        Zend_Tool_Framework_Manifest_MetadataManifestable,
        Zend_Tool_Framework_Manifest_Indexable
{
    
    public function getIndex()
    {
        return 5;
    }
    
    public function getProviders()
    {
        return new Zend_Tool_Framework_Manifest_ProviderOne();
    }
    
    public function getActions()
    {
        return new Zend_Tool_Framework_Manifest_ActionOne();
    }
    
    public function getMetadata()
    {
        return new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooOne', 'value' => 'Bar'));
    }
    
}
