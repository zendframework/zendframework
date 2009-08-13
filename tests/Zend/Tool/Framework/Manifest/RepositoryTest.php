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

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Tool_Framework_Manifest_Repository
 */
require_once 'Zend/Tool/Framework/Manifest/Repository.php';

require_once 'Zend/Tool/Framework/Registry.php';
require_once 'Zend/Tool/Framework/Provider/Repository.php';
require_once 'Zend/Tool/Framework/Action/Repository.php';

require_once '_files/ManifestGoodOne.php';
require_once '_files/ManifestGoodTwo.php';
require_once '_files/ManifestBadProvider.php';
require_once '_files/ManifestBadMetadata.php';


/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Manifest
 */
class Zend_Tool_Framework_Manifest_RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Tool_Framework_Registry
     */
    protected $_registry = null;
    
    /**
     * @var Zend_Tool_Framework_Manifest_Repository
     */
    protected $_repository = null;
    
    public function setup()
    {
        $this->_repository = new Zend_Tool_Framework_Manifest_Repository();
        
        // setup the registry components required to test with
        $this->_registry = new Zend_Tool_Framework_Registry();
        $this->_registry->setProviderRepository(new Zend_Tool_Framework_Provider_Repository());
        $this->_registry->setActionRepository(new Zend_Tool_Framework_Action_Repository());
        $this->_registry->setManifestRepository($this->_repository);
    }
    
    public function teardown()
    {
        $this->_registry->reset();
        $this->_repository = null;
    }
    
    public function testAddManfestsWillPersistManifests()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodTwo());
        $this->assertEquals(2, count($this->_repository->getManifests()));
        
        $actionRepository = $this->_registry->getActionRepository();
        $actionRepository->process();
        
        $providerRepository = $this->_registry->getProviderRepository();
        $providerRepository->process();
        
        $actions = $actionRepository->getActions();
        $this->assertArrayHasKey('actionone', $actions);
        $this->assertArrayHasKey('actiontwo', $actions);
        $this->assertArrayHasKey('foo', $actions);
        
        $providers = $providerRepository->getProviders();
        $this->assertArrayHasKey('providerone', $providers);
        $this->assertArrayHasKey('providertwo', $providers);
        
    }
    
    public function testAddManfestsWillPersistManifestsAndObeyIndex()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodTwo());
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        
        
        $manifests = $this->_repository->getManifests();
        
        $this->assertEquals(2, count($manifests));
        $this->assertTrue(array_shift($manifests) instanceof Zend_Tool_Framework_Manifest_ManifestGoodOne);
        $this->assertTrue(array_shift($manifests) instanceof Zend_Tool_Framework_Manifest_ManifestGoodTwo);
        
    }
    
    /**
     * @expectedException Zend_Tool_Framework_Manifest_Exception
     */
    public function testAddManifestThrowsExceptionOnBadGetProviders()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestBadProvider());
    }
    
    public function testProcessAddsMetadataToManifest()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodTwo());
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        $this->_repository->process();
        
        //die(); // @todo ensure that we check whats actually in the repository
        $this->assertEquals(3, count($this->_repository));
        $this->assertEquals(2, count($this->_repository->getManifests()));
    }
    
    /**
     * @expectedException Zend_Tool_Framework_Manifest_Exception
     */
    public function testProcessThrowsExceptionOnBadMetadata()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestBadMetadata());
        $this->_repository->process();
    }
    
    public function testRepositoryIsCastableToString()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodTwo());
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        $this->_repository->process();
        
        $expected = 'Basic' . PHP_EOL . '    Type: Basic, Name: FooOne, Value: Bar' . PHP_EOL 
            . '    Type: Basic, Name: FooTwo, Value: Baz1' . PHP_EOL
            . '    Type: Basic, Name: FooThree, Value: Baz2' . PHP_EOL;
        
        $this->assertEquals($expected, (string) $this->_repository);
    }
    
    public function testRepositoryIsCountable()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        $this->_repository->process();
        
        $this->assertTrue($this->_repository instanceof Countable);
        $this->assertEquals(1, count($this->_repository));
    }
    
    public function testRepositoryIsIterable()
    {
        $this->_repository->addManifest(new Zend_Tool_Framework_Manifest_ManifestGoodOne());
        $this->_repository->process();
        
        $this->assertTrue($this->_repository instanceof Traversable);
        foreach ($this->_repository as $thing) {
            $this->assertTrue(true);
        }
    }
    
    public function testManifestGetMetadatasCollectionSearchWorks()
    {
        $metadata1 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Foo',
            'value' => 'Bar',
            ));
        
        $metadata2 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Bar',
            'value' => 'Baz',
            ));
            
        $metadata3 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Baz',
            'value' => 'Foo',
            ));
            
        $this->_repository->addMetadata($metadata1);
        $this->_repository->addMetadata($metadata2);
        $this->_repository->addMetadata($metadata3);
            
        $resultMetadatas = $this->_repository->getMetadatas(array('name' => 'Bar'));
        $this->assertEquals(1, count($resultMetadatas));
        $this->assertTrue($metadata2 === array_shift($resultMetadatas));
        
        
    }
    
    public function testManifestGetMetadataSingularSearchWorks()
    {
        $metadata1 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Foo',
            'value' => 'Bar',
            ));
        
        $metadata2 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Bar',
            'value' => 'Baz',
            ));
            
        $metadata3 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Baz',
            'value' => 'Foo',
            ));
            
        $this->_repository->addMetadata($metadata1);
        $this->_repository->addMetadata($metadata2);
        $this->_repository->addMetadata($metadata3);
        
        $resultMetadata = $this->_repository->getMetadata(array('name' => 'Baz'));
        $this->assertTrue($metadata3 === $resultMetadata);
        
    }
    
    public function testManifestGetMetadatasCollectionSearchWorksWithNonExistentProperties()
    {
        $metadata1 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Foo',
            'value' => 'Bar',
            ));
        
        $metadata2 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Bar',
            'value' => 'Baz',
            ));
            
        $metadata3 = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Baz',
            'value' => 'Foo',
            ));
            
        $this->_repository->addMetadata($metadata1);
        $this->_repository->addMetadata($metadata2);
        $this->_repository->addMetadata($metadata3);
            
        $resultMetadatas = $this->_repository->getMetadatas(array('name' => 'Bar', 'blah' => 'boo'));
        $this->assertEquals(1, count($resultMetadatas));
        
        $resultMetadatas = $this->_repository->getMetadatas(array('name' => 'Bar', 'blah' => 'boo'), false);
        $this->assertEquals(0, count($resultMetadatas));
        //$this->assertTrue($metadata2 === array_shift($resultMetadatas));
        
    }
    
}
