<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
