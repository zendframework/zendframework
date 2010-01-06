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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 18386 2009-09-23 20:44:43Z ralph $
 */

/**
 * @see Zend_Tool_Project_Provider_Abstract
 */
require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * @see Zend_Tool_Framework_Provider_Interactable
 */
require_once 'Zend/Tool/Framework/Provider/Interactable.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_DbAdapter
    extends Zend_Tool_Project_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Interactable, Zend_Tool_Framework_Provider_Pretendable
{
    
    protected $_appConfigFilePath = null;
    
    protected $_config = null;
    
    protected $_sectionName = 'production';
    
    public function configure($dsn = null, $interactivelyPrompt = false, $sectionName = 'production')
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
        
        $appConfigFileResource = $profile->search('applicationConfigFile');
                
        if ($appConfigFileResource == false) {
            throw new Zend_Tool_Project_Exception('A project with an application config file is required to use this provider.');
        }
        
        $this->_appConfigFilePath = $appConfigFileResource->getPath();
        
        $this->_config = new Zend_Config_Ini($this->_appConfigFilePath, null, array('skipExtends' => true, 'allowModifications' => true));
        
        if ($sectionName != 'production') {
            $this->_sectionName = $sectionName;
        }
        
        if (!isset($this->_config->{$this->_sectionName})) {
            throw new Zend_Tool_Project_Exception('The config does not have a ' . $this->_sectionName . ' section.');
        }
        
        if (isset($this->_config->{$this->_sectionName}->resources->db)) {
            throw new Zend_Tool_Project_Exception('The config already has a db resource configured in section ' . $this->_sectionName . '.');
        }
        
        if ($dsn) {
            $this->_configureViaDSN($dsn);
        } elseif ($interactivelyPrompt) {
            $this->_promptForConfig();
        } else {
            echo 'Nothing to do!';
        }
        
        
    }
    
    protected function _configureViaDSN($dsn)
    {
        $dsnVars = array();
        
        if (strpos($dsn, '=') === false) {
            throw new Zend_Tool_Project_Provider_Exception('At least one name value pair is expected, typcially '
                . 'in the format of "adapter=Mysqli&username=uname&password=mypass&dbname=mydb"' 
                );
        }
        
        parse_str($dsn, $dsnVars);

        $dbConfigValues = array();
        
        if (isset($dsnVars['adapter'])) {
            $dbConfigValues['adapter'] = $dsnVars['adapter'];
            unset($dsnVars['adapter']);
        }
        
        $dbConfigValues['params'] = $dsnVars;
        
        $isPretend = $this->_registry->getRequest()->isPretend();
        
        $content = $this->_writeToApplicationConfig($dbConfigValues, $isPretend);
        
        $response = $this->_registry->getResponse();
        
        if ($isPretend) {
            $response->appendContent('A db configuration for the ' . $this->_sectionName
                . ' would be written to the application config file with the following contents: '
                );
            $response->appendContent($content);
        } else {
            $response->appendContent('A db configuration for the ' . $this->_sectionName
                . ' has been written to the application config file.'
                );
        }
    }
    
    protected function _promptForConfig()
    {
        echo '//@todo';
    }

    protected function _promtForConfigPdoMysql()
    {
        $r = array(
            'username' => 'Username',
            'password' => 'Password',
            'dbname'   => 'Database',
            'driver_options' => array(
                
                )
            );
    }

    protected function _writeToApplicationConfig($configValues, $isPretend = false)
    {
        $configKeyNames = array('resources', 'db');
        
        $newDbLines = array();
        
        $rii = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($configValues),
            RecursiveIteratorIterator::SELF_FIRST
            );
        
        $lastDepth = 0;
        
        foreach ($rii as $name => $value) {
            if ($lastDepth > $rii->getDepth()) {
                array_pop($configKeyNames);
            }
            
            $lastDepth = $rii->getDepth();
            
            if (is_array($value)) {
                array_push($configKeyNames, $name);
            } else {
                $newDbLines[] = implode('.', $configKeyNames) . '.' . $name . ' = "' . $value . "\"\n";
            }
        }
        
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
