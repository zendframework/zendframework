<?php

require_once 'Zend/Tool/Framework/Manifest/ActionManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/MetadataManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/Indexable.php';
require_once 'Zend/Tool/Framework/Metadata/Basic.php';

require_once 'ProviderTwo.php';
require_once 'ActionTwo.php';

class Zend_Tool_Framework_Manifest_ManifestGoodTwo 
    implements Zend_Tool_Framework_Manifest_ActionManifestable, 
        Zend_Tool_Framework_Manifest_ProviderManifestable,
        Zend_Tool_Framework_Manifest_MetadataManifestable,
        Zend_Tool_Framework_Manifest_Indexable,
        Zend_Tool_Framework_Registry_EnabledInterface
{
    
    protected $_registry = null;
    
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
    }
    
    public function getIndex()
    {
        return 10;
    }
    
    public function getProviders()
    {
        return array(
            new Zend_Tool_Framework_Manifest_ProviderTwo()
            );
    }
    
    public function getActions()
    {
        return array(
            new Zend_Tool_Framework_Manifest_ActionTwo(),
            'Foo'
            );
    }
    
    public function getMetadata()
    {
        return array(
            new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooThree', 'value' => 'Baz2'))
            );
            
    }
    
}
