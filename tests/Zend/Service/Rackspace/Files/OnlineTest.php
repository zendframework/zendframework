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
 * @package    Zend\Service\Rackspace
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Rackspace\Files;
use Zend\Service\Rackspace\Files as RackspaceFiles;
use Zend\Service\Rackspace\Files\ContainerList;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * Test helper
 */

/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Files
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Service
 * @group      Zend\Service\Rackspace
 * @group      Zend\Service\Rackspace\Files
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Rackspace Servers object
     *
     * @var Zend\Service\Rackspace\Servers
     */
    protected static $rackspace;
    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected static $httpClientAdapterSocket;
    /**
     * Metadata for container/object test
     * 
     * @var array 
     */
    protected static $metadata;
    /**
     * Another metadata for container/object test
     * 
     * @var array 
     */
    protected static $metadata2;
    /**
     * SetUpBerofeClass
     */
    public static function setUpBeforeClass()
    {
        if (!constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED')) {
            self::markTestSkipped('Zend\Service\Rackspace\TFiles online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER') || !defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY')) {
            self::markTestSkipped('Constants User and Key have to be set.');
        }

        self::$rackspace = new RackspaceFiles(TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER,
                                       TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY);

        self::$httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        self::$rackspace->getHttpClient()
                        ->setAdapter(self::$httpClientAdapterSocket);
        
        self::$metadata =  array (
            'foo'  => 'bar',
            'foo2' => 'bar2'
        );
        
        self::$metadata2 = array (
            'hello' => 'world'
        );
    }
    /**
     * Set up the test case
     *
     * @return void
     */
    public function setUp()
    {
        // terms of use compliance: safe delay between each test
        sleep(1);
    }
    
    public function testCreateContainer()
    {
        $container= self::$rackspace->createContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,self::$metadata);
        $this->assertTrue($container!==false);
        $this->assertEquals($container->getName(),TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
    }

    public function testGetCountContainers()
    {
        $num= self::$rackspace->getCountContainers();
        $this->assertTrue($num>0);
    }
    
    public function testGetContainer()
    {
        $container= self::$rackspace->getContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($container!==false);
        $this->assertEquals($container->getName(),TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
    }
    
    public function testGetContainers()
    {
        $containers= self::$rackspace->getContainers();
        $this->assertTrue($containers!==false);
        $found=false;
        foreach ($containers as $container) {
            if ($container->getName()==TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME) {
                $found=true;
                break;
            }
        } 
        $this->assertTrue($found);
    }
    
    public function testGetMetadataContainer()
    {
        $data= self::$rackspace->getMetadataContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($data!==false);
        $this->assertEquals($data['name'],TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertEquals($data['metadata'],self::$metadata);
        
    }
    
    public function testGetInfoAccount()
    {
        $data= self::$rackspace->getInfoAccount();
        $this->assertTrue($data!==false);
        $this->assertTrue($data['tot_containers']>0);
    }
    
    public function testStoreObject()
    {
        $content= 'This is a test!';
        $result= self::$rackspace->storeObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME, 
                                               TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                               $content,
                                               self::$metadata);
        $this->assertTrue($result);
    }
    
    public function testGetObject()
    {
        $object= self::$rackspace->getObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME, 
                                             TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertTrue($object!==false);
        $this->assertEquals($object->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
    }

    public function testCopyObject()
    {
        $result= self::$rackspace->copyObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy');
        $this->assertTrue($result);
    }

    public function testGetObjects()
    {
        $objects= self::$rackspace->getObjects(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($objects!==false);
        
        $this->assertEquals($objects[0]->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertEquals($objects[1]->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy');
    }
    
    public function testGetSizeContainers()
    {
        $size= self::$rackspace->getSizeContainers();
        $this->assertTrue($size!==false);
        $this->assertTrue(is_int($size));
    }
    
    public function testGetCountObjects()
    {
        $count= self::$rackspace->getCountObjects();
        $this->assertTrue($count!==false);
        $this->assertTrue(is_int($count));
    }
    
    public function testSetMetadataObject()
    {
        $result= self::$rackspace->setMetadataObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                     TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                                     self::$metadata2);
        $this->assertTrue($result);
    }
    
    public function testGetMetadataObject()
    {
        $data= self::$rackspace->getMetadataObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                   TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertTrue($data!==false);
        $this->assertEquals($data['metadata'],self::$metadata2);
    }
    
    public function testEnableCdnContainer()
    {
        $data= self::$rackspace->enableCdnContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($data!==false);
        $this->assertTrue(is_array($data));
        $this->assertTrue(!empty($data['cdn_uri']));
        $this->assertTrue(!empty($data['cdn_uri_ssl']));
    }
    
    public function testGetCdnContainers()
    {
        $containers= self::$rackspace->getCdnContainers();
        $this->assertTrue($containers!==false);
        $found= false;
        foreach ($containers as $container) {
            if ($container->getName()==TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME) {
                $found= true;
                break;
            }
        }
        $this->assertTrue($found);
    }
    
    public function testUpdateCdnContainer()
    {
        $data= self::$rackspace->updateCdnContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,null,false);
        $this->assertTrue($data!==false);
    }

    
    public function testDeleteObject()
    {
        $this->assertTrue(self::$rackspace->deleteObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                         TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME));
    }
    
    public function testDeleteObject2()
    {
        $this->assertTrue(self::$rackspace->deleteObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                         TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy'));
    }
    
    public function testDeleteContainer()
    {
        $this->assertTrue(self::$rackspace->deleteContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME));
    }
  
}
