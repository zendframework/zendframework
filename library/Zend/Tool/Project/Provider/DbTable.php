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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Provider;

use Zend\Tool\Project\Profile\Profile as ProjectProfile;

/**
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\Word\UnderscoreToCamelCase
 * @uses       \Zend\Tool\Framework\Provider\Pretendable
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @uses       \Zend\Tool\Project\Provider\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbTable 
    extends AbstractProvider
    implements \Zend\Tool\Framework\Provider\Pretendable
{
    
    protected $_specialties = array('FromDatabase');
    
    /**
     * @var \Zend\Filter\FilterChain
     */
    protected $_nameFilter = null;
    
    public static function createResource(ProjectProfile $profile, $dbTableName, $actualTableName, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'modelsDirectory';
        
        $modelsDirectory = $profile->search($profileSearchParams);
        
        if (!($modelsDirectory instanceof ProjectProfile\Resource)) {
            throw new Exception\RuntimeException(
                'A models directory was not found' .
                (($moduleName) ? ' for module ' . $moduleName . '.' : '.')
                );
        }
        
        if (!($dbTableDirectory = $modelsDirectory->search('DbTableDirectory'))) {
            $dbTableDirectory = $modelsDirectory->createResource('DbTableDirectory');
        }
        
        $dbTableFile = $dbTableDirectory->createResource('DbTableFile', array('dbTableName' => $dbTableName, 'actualTableName' => $actualTableName));
        
        return $dbTableFile;
    }
    
    public static function hasResource(ProjectProfile $profile, $dbTableName, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'modelsDirectory';
        
        $modelsDirectory = $profile->search($profileSearchParams);
        
        if (!($modelsDirectory instanceof ProjectProfile\Resource)
            || !($dbTableDirectory = $modelsDirectory->search('DbTableDirectory'))) {
            return false;
        }
        
        $dbTableFile = $dbTableDirectory->search(array('DbTableFile' => array('dbTableName' => $dbTableName)));
        
        return ($dbTableFile instanceof ProjectProfile\Resource) ? true : false;
    }
      
    
    public function create($name, $actualTableName, $module = null, $forceOverwrite = false)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        // Check that there is not a dash or underscore, return if doesnt match regex
        if (preg_match('#[_-]#', $name)) {
            throw new Exception\RuntimeException('DbTable names should be camel cased.');
        }
        
        $originalName = $name;
        $name = ucfirst($name);
        
        if ($actualTableName == '') {
            throw new Exception\RuntimeException('You must provide both the DbTable name as well as the actual db table\'s name.');
        }
        
        if (self::hasResource($this->_loadedProfile, $name, $module)) {
            throw new Exception\RuntimeException('This project already has a DbTable named ' . $name);
        }

        // get request/response object
        $request = $this->_registry->getRequest();
        $response = $this->_registry->getResponse();
        
        // alert the user about inline converted names
        $tense = (($request->isPretend()) ? 'would be' : 'is');
        
        if ($name !== $originalName) {
            $response->appendContent(
                'Note: The canonical model name that ' . $tense
                    . ' used with other providers is "' . $name . '";'
                    . ' not "' . $originalName . '" as supplied',
                array('color' => array('yellow'))
                );
        }
        
        try {
            $tableResource = self::createResource($this->_loadedProfile, $name, $actualTableName, $module);
        } catch (\Exception $e) {
            $response = $this->_registry->getResponse();
            $response->setException($e);
            return;
        }

        // do the creation
        if ($request->isPretend()) {
            $response->appendContent('Would create a DbTable at '  . $tableResource->getContext()->getPath());
        } else {
            $response->appendContent('Creating a DbTable at ' . $tableResource->getContext()->getPath());
            $tableResource->create();
            $this->_storeProfile();
        }
    }
    
    public function createFromDatabase($module = null, $forceOverwrite = false)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
        
        $bootstrapResource = $this->_loadedProfile->search('BootstrapFile');
        
        /* @var $zendApp Zend\Application */
        $zendApp = $bootstrapResource->getApplicationInstance();
        
        try {
            $zendApp->bootstrap('db');
        } catch (\Zend\Application\Exception $e) {
            throw new Exception\RuntimeException('Db resource not available, you might need to configure a DbAdapter.');
            return;
        }
        
        /* @var $db Zend\Db\Adapter\Abstract */
        $db = $zendApp->getBootstrap()->getResource('db');
        
        $tableResources = array();
        foreach ($db->listTables() as $actualTableName) {
            
            $dbTableName = $this->_convertTableNameToClassName($actualTableName);
            
            if (!$forceOverwrite && self::hasResource($this->_loadedProfile, $dbTableName, $module)) {
                throw new Exception\RuntimeException(
                    'This DbTable resource already exists, if you wish to overwrite it, '
                    . 'pass the "forceOverwrite" flag to this provider.'
                    );
            }
            
            $tableResources[] = self::createResource(
                $this->_loadedProfile,
                $dbTableName,
                $actualTableName,
                $module
                );
        }
        
        if (count($tableResources) == 0) {
            $this->_registry->getResponse()->appendContent('There are no tables in the selected database to write.');
        }
        
        // do the creation
        if ($this->_registry->getRequest()->isPretend()) {

            foreach ($tableResources as $tableResource) {
                $this->_registry->getResponse()->appendContent('Would create a DbTable at '  . $tableResource->getContext()->getPath());
            }

        } else {

            foreach ($tableResources as $tableResource) {
                $this->_registry->getResponse()->appendContent('Creating a DbTable at ' . $tableResource->getContext()->getPath());
                $tableResource->create();
            }

            $this->_storeProfile();
        }
        
        
    }
    
    protected function _convertTableNameToClassName($tableName)
    {
        if ($this->_nameFilter == null) {
            $this->_nameFilter = new \Zend\Filter\FilterChain();
            $this->_nameFilter
                 ->attach(new \Zend\Filter\Word\UnderscoreToCamelCase());
        }
        
        return $this->_nameFilter->filter($tableName);
    }
    
}
