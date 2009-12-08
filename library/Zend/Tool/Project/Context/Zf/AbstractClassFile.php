<?php

abstract class Zend_Tool_Project_Context_Zf_AbstractClassFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    public function getFullClassName($localClassName, $classContextName = null)
    {

        $currentResource = $this->_resource;
        do {
            $resourceName = $currentResource->getName();
            if ($resourceName == 'ApplicationDirectory' || $resourceName == 'ModuleDirectory') {
                $containingResource = $currentResource;
                break;
            }
        } while ($currentResource instanceof Zend_Tool_Project_Profile_Resource
            && $currentResource = $currentResource->getParentResource());
        
        $fullClassName = '';
            
        if (isset($containingResource)) {
            if ($containingResource->getName() == 'ApplicationDirectory') {
                $prefix = $containingResource->getAttribute('classNamePrefix');
                $fullClassName = $prefix;
            } elseif ($containingResource->getName() == 'ModuleDirectory') {
                $prefix = $containingResource->getAttribute('moduleName') . '_';
                $fullClassName = $prefix;    
            }
        }
                
        if ($classContextName) {
            $fullClassName .= rtrim($classContextName, '_') . '_';
        }
        $fullClassName .= $localClassName;
                    
        return $fullClassName;
    }

}
