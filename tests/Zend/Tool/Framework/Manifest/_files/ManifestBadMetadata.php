<?php

require_once 'Zend/Tool/Framework/Manifest/MetadataManifestable.php';
require_once 'Zend/Tool/Framework/Metadata/Basic.php';

class Zend_Tool_Framework_Manifest_ManifestBadMetadata 
    implements Zend_Tool_Framework_Manifest_MetadataManifestable
{
    
    public function getMetadata()
    {
        return array(
            new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new ArrayObject()
            );
            
    }
    
}