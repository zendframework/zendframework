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

use Zend\Tool\Framework\Client,
    Zend\Tool\Project\Profile\Profile as ProjectProfile;

/**
 * @uses       \Zend\Tool\Framework\Client\Exception
 * @uses       \Zend\Tool\Project\Profile\Profile
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Project
    extends AbstractProvider
{

    protected $_specialties = array('Info');

    /**
     * create()
     *
     * @param string $path
     * @param string $nameOfProfile shortName=n
     * @param string $fileOfProfile shortName=f
     */
    public function create($path, $nameOfProfile = null, $fileOfProfile = null)
    {
        if ($path == null) {
            $path = getcwd();
        } else {
            $path = trim($path);
            if (!file_exists($path)) {
                $created = mkdir($path);
                if (!$created) {
                    throw new Exception\RuntimeException(sprintf(
                        'Could not create requested project directory "%s"',
                        $path
                    ));
                }
            }
            $path = str_replace('\\', '/', realpath($path));
        }

        $profile = $this->_loadProfile(self::NO_PROFILE_RETURN_FALSE, $path);

        if ($profile !== false) {
            throw new Exception\RuntimeException('A project already exists here');
        }

        $profileData = null;

        if ($fileOfProfile != null && file_exists($fileOfProfile)) {
            $profileData = file_get_contents($fileOfProfile);
        }

        $storage = $this->_registry->getStorage();
        if ($profileData == '' && $nameOfProfile != null && $storage->isEnabled()) {
            $profileData = $storage->get('project/profiles/' . $nameOfProfile . '.xml');
        }

        if ($profileData == '') {
            $profileData = $this->_getDefaultProfile();
        }

        $newProfile = new ProjectProfile(array(
            'projectDirectory' => $path,
            'profileData' => $profileData
            ));

        $newProfile->loadFromData();

        $response = $this->_registry->getResponse();
        
        $response->appendContent('Creating project at ' . $path);
        $response->appendContent('Note: ', array('separator' => false, 'color' => 'yellow'));
        $response->appendContent(
            'This command created a web project, '
            . 'for more information setting up your VHOST, please see docs/README');

        foreach ($newProfile->getIterator() as $resource) {
            $resource->create();
        }
    }

    public function show()
    {
        $this->_registry->getResponse()->appendContent('You probably meant to run "show project.info".', array('color' => 'yellow'));
    }

    public function showInfo()
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_RETURN_FALSE);
        if (!$profile) {
            $this->_registry->getResponse()->appendContent('No project found.');
        } else {
            $this->_registry->getResponse()->appendContent('Working with project located at: ' . $profile->getAttribute('projectDirectory'));
        }
    }

    protected function _getDefaultProfile()
    {
        $data = <<<'EOS'
<?xml version="1.0" encoding="UTF-8"?>
<projectProfile type="default" version="1.10">
    <projectDirectory>
        <projectProfileFile />
        <applicationDirectory>
            <apisDirectory enabled="false" />
            <configsDirectory>
                <applicationConfigFile type="ini" />
            </configsDirectory>
            <controllersDirectory>
                <controllerFile controllerName="Index">
                    <actionMethod actionName="index" />
                </controllerFile>
                <controllerFile controllerName="Error" />
            </controllersDirectory>
            <formsDirectory enabled="false" />
            <layoutsDirectory enabled="false" />
            <modelsDirectory />
            <modulesDirectory enabled="false" />
            <viewsDirectory>
                <viewScriptsDirectory>
                    <viewControllerScriptsDirectory forControllerName="Index">
                        <viewScriptFile forActionName="index" />
                    </viewControllerScriptsDirectory>
                    <viewControllerScriptsDirectory forControllerName="Error">
                        <viewScriptFile forActionName="error" />
                    </viewControllerScriptsDirectory>
                </viewScriptsDirectory>
                <viewHelpersDirectory />
                <viewFiltersDirectory enabled="false" />
            </viewsDirectory>
            <bootstrapFile />
        </applicationDirectory>
        <dataDirectory enabled="false">
            <cacheDirectory enabled="false" />
            <searchIndexesDirectory enabled="false" />
            <localesDirectory enabled="false" />
            <logsDirectory enabled="false" />
            <sessionsDirectory enabled="false" />
            <uploadsDirectory enabled="false" />
        </dataDirectory>
        <docsDirectory>
            <file filesystemName="README.txt" defaultContentCallback="Zend\Tool\Project\Provider\Project::getDefaultReadmeContents"/>
        </docsDirectory>
        <libraryDirectory>
            <zfStandardLibraryDirectory enabled="false" />
        </libraryDirectory>
        <publicDirectory>
            <publicStylesheetsDirectory enabled="false" />
            <publicScriptsDirectory enabled="false" />
            <publicImagesDirectory enabled="false" />
            <publicIndexFile />
            <htaccessFile />
        </publicDirectory>
        <projectProvidersDirectory enabled="false" />
        <temporaryDirectory enabled="false" />
        <testsDirectory>
            <testPHPUnitConfigFile />
            <testApplicationDirectory>
                <testApplicationBootstrapFile />
            </testApplicationDirectory>
            <testLibraryDirectory>
                <testLibraryBootstrapFile />
            </testLibraryDirectory>
        </testsDirectory>
    </projectDirectory>
</projectProfile>
EOS;
        return $data;
    }
    
    public static function getDefaultReadmeContents($caller = null)
    {
        $projectDirResource = $caller->getResource()->getProfile()->search('projectDirectory');
        if ($projectDirResource) {
            $name = ltrim(strrchr($projectDirResource->getPath(), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
            $path = $projectDirResource->getPath() . '/public';
        } else {
            $path = '/path/to/public';
        }
        
        return <<< EOS
README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or 
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "$path"
   ServerName $name.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
   <Directory "$path">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>
    
</VirtualHost>

EOS;
    }
}
