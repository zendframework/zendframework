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
 * @package    ZendTest_Cloud_Infrastructure_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\Infrastructure\Adapter;

use Zend\Http\Client as HttpClient,
    Zend\Http\Client\Adapter\Test as HttpTest,
    Zend\Cloud\Infrastructure\Adapter\Rackspace,
    Zend\Cloud\Infrastructure\Instance,
    Zend\Cloud\Infrastructure\Factory as CloudFactory;

class RackspaceOfflineTest extends \PHPUnit_Framework_TestCase
{
    const IMAGE_ID    = '49';
    const FLAVOR_ID   = '1';
    const SERVER_ID   = '20265545';
    const SERVER_NAME = 'ZFunitTestImage';
    const SERVER_IP   = '50.57.38.207';
    const SERVER_PASS = 'ZFunitTestImageWt040ivTR';
    /**
     * Reference to Infrastructure object
     *
     * @var \Zend\Cloud\Infrastructure\Adapter
     */
    protected $infrastructure;

    /**
     * Socket based HTTP client adapter
     *
     * @var \Zend\Http\Client\Adapter\Test
     */
    protected $httpClientAdapterTest;
    
    /**
     * Image ID of the instance
     * 
     * @var string
     */
    protected static $instanceId;
    
    /**
     * Setup for each test
     */
    public function setUp()
    {
        $this->infrastructure = CloudFactory::getAdapter(array( 
            CloudFactory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Rackspace',
            Rackspace::RACKSPACE_USER   => 'test', 
            Rackspace::RACKSPACE_KEY    => 'test', 
            Rackspace::RACKSPACE_REGION => 'USA'  
        )); 

        $this->httpClientAdapterTest = new HttpTest();

        $this->infrastructure->getAdapter()
                             ->getHttpClient()
                             ->setAdapter($this->httpClientAdapterTest);
        
        $shortClassName = 'RackspaceTest';      
        // load the HTTP response (from a file)
        $filename= dirname(__FILE__) . '/_files/' . $shortClassName . '_'. $this->getName().'.response';
        
        if (file_exists($filename)) {
            // authentication (from file)
            $content = file_get_contents(dirname(__FILE__) . '/_files/'.$shortClassName . '_testAuthenticate.response');
            $this->httpClientAdapterTest->setResponse($content);
            $this->assertTrue($this->infrastructure->getAdapter()->authenticate(),'Authentication failed');
            // set the specific API response
            $content = file_get_contents($filename);
            $this->httpClientAdapterTest->setResponse($content); 
        }
        
    }
       
    /**
     * Get Config Array
     * 
     * @return array
     */ 
    static function getConfigArray()
    {
         return array(
            CloudFactory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Rackspace',
            Rackspace::RACKSPACE_USER   => 'test',
            Rackspace::RACKSPACE_KEY    => 'test',
            Rackspace::RACKSPACE_REGION => 'USA'
        );
    }
    
    /**
     * Test all the constants of the class
     */
    public function testConstants()
    {
        $this->assertEquals('rackspace_user', Rackspace::RACKSPACE_USER);
        $this->assertEquals('rackspace_key', Rackspace::RACKSPACE_KEY);
        $this->assertEquals('rackspace_region', Rackspace::RACKSPACE_REGION);
        $this->assertEquals('USA', Rackspace::RACKSPACE_ZONE_USA);
        $this->assertEquals('UK', Rackspace::RACKSPACE_ZONE_UK);
        $this->assertTrue(Rackspace::MONITOR_CPU_SAMPLES>0);
    }
    /**
     * Test construct with missing params
     */
    public function testConstructExceptionMissingParams() 
    {
        $this->setExpectedException(
            'Zend\Cloud\Infrastructure\Adapter\Exception\InvalidArgumentException',
            'Invalid options provided'
        );
        $instance = new Rackspace('foo');
    }
    /**
     * Test authentication failed
     */
    public function testAuthenticationFailed()
    {
        $this->setExpectedException(
            'Zend\Service\Rackspace\Exception\RuntimeException',
            'Authentication failed, you need a valid token to use the Rackspace API'
        );
        $images = $this->infrastructure->imagesInstance();
    }
    /**
     * Test getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertInstanceOf('Zend\Service\Rackspace\Servers',$this->infrastructure->getAdapter());
    }
    /**
     * Test create an instance
     */
    public function testCreateInstance()
    {
        $options = array (
            'imageId'  => self::IMAGE_ID,
            'flavorId' => self::FLAVOR_ID,
            'metadata' => array (
                'foo' => 'bar'
            )
        );
        $instance = $this->infrastructure->createInstance(self::SERVER_NAME, $options);
        
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(self::IMAGE_ID, $instance->getImageId());
        $this->assertEquals(self::SERVER_ID, $instance->getId());
        $metadata = $instance->getMetadata();
        $this->assertTrue(is_array($metadata));
        $this->assertEquals('bar', $metadata['foo']);
        $this->assertEquals(self::SERVER_IP, $instance->getPublicDns());
        $this->assertEquals(self::SERVER_PASS, $instance->getAttribute('adminPass'));
        $this->assertTrue(is_array($instance->getAttribute('addresses')));
    }
 
    /**
     * Test list of an instance
     */
    public function testListInstance()
    {
        $instances = $this->infrastructure->listInstances();
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(count($instances), 1);
        $this->assertEquals(self::SERVER_ID, $instances[0]->getId());
    }
    /**
     * Test images instance
     */
    public function testImagesInstance()
    {
        $images = $this->infrastructure->imagesInstance();
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(count($images),29);
        $this->assertEquals('Gentoo 10.1', $images[0]->getName());
        $this->assertEquals('Gentoo 10.1', $images[0]->getDescription());
        $this->assertEquals('19', $images[0]->getId());
        $this->assertEquals('linux', $images[0]->getPlatform());
        $this->assertEquals('i386', $images[0]->getArchitecture());
        $this->assertEquals('ACTIVE', $images[0]->getAttribute('status'));
        $this->assertEquals('2009-12-15T15:43:39-06:00', $images[0]->getAttribute('updated'));
    }
    /**
     * Test zones instance
     */
    public function testZonesInstance()
    {
        $zones = $this->infrastructure->zonesInstance();
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertTrue(is_array($zones));
        $this->assertTrue(in_array(Rackspace::RACKSPACE_ZONE_UK,$zones));
        $this->assertTrue(in_array(Rackspace::RACKSPACE_ZONE_USA,$zones));
    }
    /**
     * Test public DNS instance
     */
    public function testPublicDnsInstance()
    {
        $dns = $this->infrastructure->publicDnsInstance(self::SERVER_ID);
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(self::SERVER_IP, $dns); 
    }
    /**
     * Test status instance
     */
    public function testStatusInstance()
    {
        $status = $this->infrastructure->statusInstance(self::SERVER_ID);
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(Instance::STATUS_RUNNING, $status); 
    }
    /**
     * Test monitor instance
     */
    public function testMonitorInstance()
    {
        $this->markTestSkipped('Test monitor instance skipped');
    }
    /**
     * Test deploy instance
     */
    public function testDeployInstance()
    {
        $this->markTestSkipped('Test deploy instance skipped');
    }
    /**
     * Test stop an instance
     */
    public function testStopInstance()
    {
        $this->markTestSkipped('Test stop instance skipped');
    }
    /**
     * Test start an instance
     */
    public function testStartInstance()
    {
        $this->markTestSkipped('Test start instance skipped');   
    }
    /**
     * Test reboot and instance
     */
    public function testRebootInstance()
    {
        $this->assertTrue($this->infrastructure->rebootInstance(self::SERVER_ID));
        $this->assertTrue($this->infrastructure->isSuccessful());
    }
    /**
     * Test destroy instance
     */
    public function testDestroyInstance()
    {
        $this->assertTrue($this->infrastructure->destroyInstance(self::SERVER_ID));
        $this->assertTrue($this->infrastructure->isSuccessful());
    }
}
