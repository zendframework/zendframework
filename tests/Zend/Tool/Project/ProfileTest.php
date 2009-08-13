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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Tool/Project/Profile.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Action
 */
class Zend_Tool_Project_ProfileTest extends PHPUnit_Framework_TestCase
{
    
    protected $_projectDirectory   = null;
    protected $_projectProfileFile = null;
    
    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_standardProfileFromData = null;
    
    public function setup()
    {
        $this->_projectDirectory   = dirname(__FILE__) . '/_files/project1/';
        $this->_projectProfileFile = dirname(__FILE__) . '/_files/.zfproject.xml.orig';

        $this->_removeProjectFiles();

        Zend_Tool_Project_Context_Repository::resetInstance();
        
        $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
        $contextRegistry->addContextsFromDirectory(dirname(__FILE__) . '/../../../../library/Zend/Tool/Project/Context/Zf/', 'Zend_Tool_Project_Context_Zf_');
        
        $this->_standardProfileFromData = new Zend_Tool_Project_Profile();
        $this->_standardProfileFromData->setAttribute('profileData',      file_get_contents($this->_projectProfileFile));
        $this->_standardProfileFromData->setAttribute('projectDirectory', $this->_projectDirectory);
    }
    
    public function teardown()
    {
        $this->_removeProjectFiles();
    }
    

    public function testAttibuteGettersAndSettersWork()
    {
        
        $profile = new Zend_Tool_Project_Profile(array('foo' => 'bar'));
        $profile->setAttributes(array('baz' => 'BAZ'));
        $profile->setAttribute('boof', 'foob');
        
        $this->assertEquals('foob', $profile->getAttribute('boof'));
        $this->assertContains('bar', $profile->getAttributes());
        $this->assertContains('BAZ', $profile->getAttributes());
        
    }
    
    public function testProfileLoadsFromExistingFileGivenProjectDirectory()
    {
        copy($this->_projectProfileFile, $this->_projectDirectory . '/.zfproject.xml');
        
        $profile = new Zend_Tool_Project_Profile();
        $profile->setAttribute('projectDirectory', $this->_projectDirectory);
        $profile->loadFromFile();
        
        // first item in here should be 'projectDirectory'
        $projectDirectoryResource = $profile->current();
        
        $this->assertEquals(1, count($profile));
        $this->assertEquals('Zend_Tool_Project_Profile_Resource', get_class($projectDirectoryResource));
        $this->assertEquals('Zend_Tool_Project_Context_System_ProjectDirectory', get_class($projectDirectoryResource->getContext()));
    }


    public function testProfileLoadsFromExistingFileGivenProfileFile()
    {
        
        $profile = new Zend_Tool_Project_Profile(array(
            'projectProfileFile' => $this->_projectProfileFile,
            'projectDirectory'   => $this->_projectDirectory
            ));
        $profile->loadFromFile();
        
        $projectDirectoryResource = $profile->current();
        
        $this->assertEquals('Zend_Tool_Project_Profile_Resource', get_class($projectDirectoryResource));
        $this->assertEquals('Zend_Tool_Project_Context_System_ProjectDirectory', get_class($projectDirectoryResource->getContext()));
    }
    
    public function testProfileFromVariousSourcesIsLoadableFromFile()
    {
        
        $profile = new Zend_Tool_Project_Profile();
        
        // no options, should return false
        $this->assertFalse($profile->isLoadableFromFile());
        
        // invalid file path, should be false
        $profile->setAttribute('projectProfileFile', $this->_projectProfileFile . '.invalid-file');
        $this->assertFalse($profile->isLoadableFromFile());
        
        // valid file path, shoudl be true
        $profile->setAttribute('projectProfileFile', $this->_projectProfileFile);
        $this->assertTrue($profile->isLoadableFromFile());
        
        // just project directory
        $profile = new Zend_Tool_Project_Profile();
        
        // shoudl be false with non existent directory
        $profile->setAttribute('projectDirectory', $this->_projectDirectory . 'non-existent/dir/');
        $this->assertFalse($profile->isLoadableFromFile());

        // should return true for proper directory
        copy($this->_projectProfileFile, $this->_projectDirectory . '/.zfproject.xml');
        $profile->setAttribute('projectDirectory', $this->_projectDirectory);
        $this->assertTrue($profile->isLoadableFromFile());
        
        
    }

    public function testLoadFromDataIsSameAsLoadFromFile()
    {

        $profile = new Zend_Tool_Project_Profile(array('projectProfileFile' => $this->_projectProfileFile));
        $profile->setAttribute('projectDirectory', $this->_projectDirectory);
        $profile->loadFromFile();
        
        $profile2 = new Zend_Tool_Project_Profile();
        $profile2->setAttribute('profileData', file_get_contents($this->_projectProfileFile));
        $profile2->setAttribute('projectDirectory', $this->_projectDirectory);
        $profile2->loadFromData();
        
        $this->assertEquals($profile->__toString(), $profile2->__toString());
    }
    
    public function testProfileCanReturnStorageData()
    {
        $this->_standardProfileFromData->loadFromData();
        $expectedValue = '<?xml version="1.0"?><projectProfile>  <projectDirectory>    <projectProfileFile/>    <applicationDirectory>      <apisDirectory enabled="false"/>      <configsDirectory>        <applicationConfigFile type="ini"/>      </configsDirectory>      <controllersDirectory>        <controllerFile controllerName="index"/>        <controllerFile controllerName="error"/>      </controllersDirectory>      <layoutsDirectory enabled="false"/>      <modelsDirectory/>      <modulesDirectory enabled="false"/>      <viewsDirectory>        <viewScriptsDirectory>          <viewControllerScriptsDirectory forControllerName="index">            <viewScriptFile scriptName="index"/>          </viewControllerScriptsDirectory>        </viewScriptsDirectory>        <viewHelpersDirectory/>        <viewFiltersDirectory enabled="false"/>      </viewsDirectory>      <bootstrapFile/>    </applicationDirectory>    <dataDirectory enabled="false">      <cacheDirectory enabled="false"/>      <searchIndexesDirectory enabled="false"/>      <localesDirectory enabled="false"/>      <logsDirectory enabled="false"/>      <sessionsDirectory enabled="false"/>      <uploadsDirectory enabled="false"/>    </dataDirectory>    <libraryDirectory>      <zfStandardLibraryDirectory/>    </libraryDirectory>    <publicDirectory>      <publicStylesheetsDirectory enabled="false"/>      <publicScriptsDirectory enabled="false"/>      <publicImagesDirectory enabled="false"/>      <publicIndexFile/>      <htaccessFile/>    </publicDirectory>    <projectProvidersDirectory enabled="false"/>  </projectDirectory></projectProfile>';
        $this->assertEquals($expectedValue, str_replace(PHP_EOL, '', $this->_standardProfileFromData->storeToData()));
    }
    
    public function testProfileCanSaveStorageDataToFile()
    {
        $this->_standardProfileFromData->loadFromData();
        $this->_standardProfileFromData->setAttribute('projectProfileFile', $this->_projectDirectory . 'my-xml-file.xml');
        $this->_standardProfileFromData->storeToFile();
        $this->assertTrue(file_exists($this->_projectDirectory . 'my-xml-file.xml'));
    }
    
    public function testProfileCanFindResource()
    {
        $profile = new Zend_Tool_Project_Profile(array(
            'projectProfileFile' => $this->_projectProfileFile,
            'projectDirectory'   => $this->_projectDirectory
            ));
        $profile->loadFromFile();
        
        $modelsDirectoryResource = $profile->search('modelsDirectory');
        
        $this->assertEquals('Zend_Tool_Project_Profile_Resource', get_class($modelsDirectoryResource));
        $this->assertEquals('Zend_Tool_Project_Context_Zf_ModelsDirectory', get_class($modelsDirectoryResource->getContext()));
        
        $publicIndexFile = $profile->search(array('publicDirectory', 'publicIndexFile'));
        
        $this->assertEquals('Zend_Tool_Project_Profile_Resource', get_class($publicIndexFile));
        $this->assertEquals('Zend_Tool_Project_Context_Zf_PublicIndexFile', get_class($publicIndexFile->getContext()));
        
    }
    
    public function testProfileCanRecursivelyCreateParentFirst()
    {
        $this->_standardProfileFromData->loadFromData();
        
        foreach ($this->_standardProfileFromData->getIterator() as $resource) {
            $resource->getContext()->create();
        }
        
        $this->assertTrue(file_exists($this->_projectDirectory . 'public/index.php'));
    }
    
    public function testProfileCanDelete()
    {
        $this->_standardProfileFromData->loadFromData();
        
        foreach ($this->_standardProfileFromData->getIterator() as $resource) {
            $resource->getContext()->create();
        }
        
        $this->assertTrue(file_exists($this->_projectDirectory . 'public/index.php'));
        
        $publicIndexFile = $this->_standardProfileFromData->search('publicIndexFile');
        $publicIndexFile->getContext()->delete();
        
        $this->assertFalse(file_exists($this->_projectDirectory . 'public/index.php'));
        
        $appConfigFile = $this->_standardProfileFromData->search('applicationConfigFile');
        $appConfigFile->getContext()->delete();
        $configsDirectory = $this->_standardProfileFromData->search('configsDirectory');
        $configsDirectory->getContext()->delete();
        
        $this->assertFalse(file_exists($this->_projectDirectory . 'application/configs'));
    }
    
    /**
     * 
     * @expectedException Zend_Tool_Project_Exception
     */
    public function testProfileThrowsExceptionOnLoadFromData()
    {
        $profile = new Zend_Tool_Project_Profile();
        
        // missing data from attributes should throw exception here
        $profile->loadFromData();
    }
    
    /**
     * 
     * @expectedException Zend_Tool_Project_Exception
     */
    public function testProfileThrowsExceptionOnLoadFromFile()
    {
        $profile = new Zend_Tool_Project_Profile();
        
        // missing file path or project path
        $profile->loadFromFile();
    }
    
    /**
     * 
     * @expectedException Zend_Tool_Project_Exception
     */
    public function testProfileThrowsExceptionOnStoreToFile()
    {
        $profile = new Zend_Tool_Project_Profile();
        
        // missing file path or project path
        $profile->storeToFile();
    }

    /**
     * 
     * @expectedException Zend_Tool_Project_Exception
     */
    public function testProfileThrowsExceptionOnLoadFromFileWithBadPathForProfileFile()
    {
        $profile = new Zend_Tool_Project_Profile();
        $profile->setAttribute('projectProfileFile', '/path/should/not/exist');
        
        // missing file path or project path
        $profile->loadFromFile();
    }
    
    protected function _removeProjectFiles()
    {
        $rdi = new RecursiveDirectoryIterator($this->_projectDirectory);
        
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::CHILD_FIRST) as $dirIteratorItem) {
            
            if (stristr($dirIteratorItem->getPathname(), '.svn')) {
                continue;
            }
            
            if ($dirIteratorItem->isDir()) {
                rmdir($dirIteratorItem->getPathname());
            } elseif ($dirIteratorItem->isFile()) {
                unlink($dirIteratorItem->getPathname());
            }
        }
    }
    
}
  
