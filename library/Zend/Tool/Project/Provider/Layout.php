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
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 18386 2009-09-23 20:44:43Z ralph $
 */

/**
 * @see Zend_Tool_Project_Provider_Abstract
 */
require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_Layout extends Zend_Tool_Project_Provider_Abstract
{
    
    public static function prepareApplicationConfig(Zend_Tool_Project_Profile $profile, $section = 'production', $layoutPath = 'layout/scripts/')
    {
        $appConfigFileResource = $profile->search('ApplicationConfigFile');
                
        if ($appConfigFileResource == false) {
            throw new Zend_Tool_Project_Exception('A project with an application config file is required to use this provider.');
        }
        
        $appConfigFilePath = $appConfigFileResource->getPath();
        
        $config = new Zend_Config_Ini($appConfigFilePath, null, array('skipExtends' => true, 'allowModifications' => true));
        
        if (!isset($config->{$section})) {
            throw new Zend_Tool_Project_Exception('The config does not have a ' . $section . ' section.');
        }
        
        $currentSection = $config->{$section};
        
        if (!isset($currentSection->resources)) {
            $currentSection->resources = array();
        }
        
        $configResources = $currentSection->resources;
        
        if (!isset($configResources->layout)) {
            $configResources->layout = array();
        }
        
        $layout = $configResources->layout;
        
        $layout->layoutPath = 'APPLICATION_PATH "layouts/scripts"';
        
        $writer = new Zend_Config_Writer_Ini(array(
            'config' => $config,
            'filename' => $appConfigFilePath
            ));
        $writer->write();
        
    }
    
    public static function createResource(Zend_Tool_Project_Profile $profile, $layoutName = 'layout')
    {
        $applicationDirectory = $profile->search('applicationDirectory');
        $layoutDirectory = $applicationDirectory->search('layoutsDirectory');
        
        if ($layoutDirectory == false) {
            $layoutDirectory = $applicationDirectory->createResource('layoutsDirectory');
        }
        
        $layoutScriptsDirectory = $layoutDirectory->search('layoutScriptsDirectory');
        
        if ($layoutScriptsDirectory == false) {
            $layoutScriptsDirectory = $layoutDirectory->createResource('layoutScriptsDirectory');
        }
        
        $layoutScriptFile = $layoutScriptsDirectory->search('layoutScriptFile', array('layoutName' => 'layout'));

        if ($layoutScriptFile == false) {
            $layoutScriptFile = $layoutScriptsDirectory->createResource('layoutScriptFile', array('layoutName' => 'layout'));
        }
        
        return $layoutScriptFile;
    }
    
    public function enable()
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
        
        self::prepareApplicationConfig($profile);
        
        $layoutScriptFile = self::createResource($profile);
        
        $layoutScriptFile->create();
        
        $this->_registry->getResponse()->appendContent(
            'Layouts have been enabled, and a default layout created at ' 
            . $layoutScriptFile->getPath()
            );
            
        $this->_registry->getResponse()->appendContent('A layout entry has been added to the application config file.');
        
    }
    
    public function disable()
    {
        
    }
    
}
