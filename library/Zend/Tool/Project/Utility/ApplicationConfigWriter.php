<?php

class Zend_Tool_Project_Utility_ApplicationConfigWriter
{
    
    public function setFilename()
    {
        
    }
    
    
    protected function _writeArrayToConfig($configArray)
    {
        
    }
    
    protected function _writeStringToConfig()
    {
        $configKeyNames = array('resources', 'db');
        
        $newDbLines = array();
        

        
        $originalLines = file($this->_appConfigFilePath);
        
        $newLines = array();
        $insideSection = false;
        
        foreach ($originalLines as $originalLineIndex => $originalLine) {
            
            if ($insideSection === false && preg_match('#^\[' . $this->_sectionName . '#', $originalLine)) {
                $insideSection = true;
            }
            
            if ($insideSection) {
                if ((trim($originalLine) == null) || ($originalLines[$originalLineIndex + 1][0] == '[')) {
                    foreach ($newDbLines as $newDbLine) {
                        $newLines[] = $newDbLine;
                    }
                    $insideSection = null;
                }
            }
            
            $newLines[] = $originalLine;
        }

        $newConfigContents = implode('', $newLines);
        
        if (!$isPretend) {
            file_put_contents($this->_appConfigFilePath, $newConfigContents);
        }
        
        return $newConfigContents;
    }
    
}
