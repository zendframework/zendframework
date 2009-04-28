<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Framework/Loader/IncludePathLoader.php';
require_once 'Zend/Tool/Framework/Manifest/Repository.php';
require_once 'Zend/Tool/Framework/Action/Repository.php';
require_once 'Zend/Tool/Framework/Provider/Repository.php';

class Zend_Tool_Framework_Loader_IncludePathLoaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Registry
     */
    protected $_registry = null;
    
    public function setUp()
    {

    }

    public function tearDown()
    {
        Zend_Tool_Framework_Registry::resetInstance();
    }
    
    /** running these tests need to happen in separate process */
    /**
    

    public function testLoaderFindsIncludePathFilesAreFound()
    {
        $loader = new Zend_Tool_Framework_Loader_IncludePathLoader();
        $loader->load();
        $files = $loader->getLoadRetrievedFiles();
        foreach ($files as $index => $file) {
            $files[$index] = substr($file, strpos($file, 'Zend'));
        }
        $this->assertContains('Zend/Tool/Framework/System/Manifest.php', $files);
    }
    
    public function testLoaderFindsIncludePathFilesAreLoaded()
    {
        $loader = new Zend_Tool_Framework_Loader_IncludePathLoader();
        $loader->load();
        $classes = $loader->getLoadLoadedClasses();
        $this->assertContains('Zend_Tool_Framework_System_Manifest', $classes);
    }

    */
    
}
