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
 * @package    Zend\Cloud\Infrastructure\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace ZendTest\Cloud\Infrastructure\Adapter;

use Zend\Http\Client as HttpClient,
    Zend\Http\Client\Adapter\Test as HttpTest,
    Zend\Cloud\Infrastructure\Adapter\Ec2,
    Zend\Cloud\Infrastructure\Instance,
    Zend\Cloud\Infrastructure\Factory as CloudFactory;

class Ec2OfflineTest extends \PHPUnit_Framework_TestCase
{
    const IMAGE_ID     = 'ami-7f418316';
    const SERVER_ID    = 'i-6cfcb00c';
    const SERVER_NAME  = 'test';
    const SERVER_IP    = 'ec2-184-72-66-78.compute-1.amazonaws.com';
    const SERVER_GROUP = 'default';
    /**
     * Reference to Infrastructure object
     *
     * @var Zend\Cloud\Infrastructure\Adapter
     */
    protected $infrastructure;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend\Http\Client\Adapter\Test
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
            CloudFactory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Ec2', 
            Ec2::AWS_ACCESS_KEY         => '0123456789', 
            Ec2::AWS_SECRET_KEY         => 'test', 
            Ec2::AWS_REGION             => 'us-east-1'     
        )); 

        $this->httpClientAdapterTest = new HttpTest();     

        // load the HTTP response (from a file)
        $shortClassName = 'Ec2Test';
        $filename= dirname(__FILE__) . '/_files/' . $shortClassName . '_'. $this->getName().'.response';

        if (file_exists($filename)) {
            $this->httpClientAdapterTest->setResponse(file_get_contents($filename)); 
        }
        
        $adapter= $this->infrastructure->getAdapter();
        
        $client = new HttpClient(null, array(
            'adapter' => $this->httpClientAdapterTest
        ));
        
        call_user_func(array($adapter,'setHttpClient'),$client);
    
    }
    /**
     * Get Config Array
     * 
     * @return array
     */ 
    static function getConfigArray()
    {
         return array(
            CloudFactory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Ec2',
            Ec2::AWS_ACCESS_KEY         => 'test',
            Ec2::AWS_SECRET_KEY         => 'test',
            Ec2::AWS_REGION             => 'us-east-1',
            Ec2::AWS_SECURITY_GROUP     => self::SERVER_GROUP
        );
    }
    
    /**
     * Test all the constants of the class
     */
    public function testConstants()
    {
        $this->assertEquals('aws_accesskey', Ec2::AWS_ACCESS_KEY);
        $this->assertEquals('aws_secretkey', Ec2::AWS_SECRET_KEY);
        $this->assertEquals('aws_region', Ec2::AWS_REGION);
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
        $image = new Ec2('foo');
    }

    /**
     * Test getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Instance',$this->infrastructure->getAdapter());
    }

    /**
     * Test create an instance
     */
    public function testCreateInstance()
    {
        $options = array (
            Instance::INSTANCE_IMAGEID => self::IMAGE_ID,
            Ec2::AWS_SECURITY_GROUP => array(self::SERVER_GROUP)
        );       
        $instance = $this->infrastructure->createInstance(self::SERVER_NAME, $options);
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(self::IMAGE_ID, $instance->getImageId());
    }

    /**
     * Test list of an instance
     */
    public function testListInstance()
    {
        $instances = $this->infrastructure->listInstances(self::$instanceId);
        $this->assertTrue($this->infrastructure->isSuccessful());
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->getId()==self::SERVER_ID) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test images instance
     */
    public function testImagesInstance()
    {
        $images = $this->infrastructure->imagesInstance();
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertEquals(count($images),5);
        $this->assertEquals('aki-00806369', $images[0]->getId());
        $this->assertEquals('linux', $images[0]->getPlatform());
        $this->assertEquals('i386', $images[0]->getArchitecture());
        $this->assertEquals('099720109477', $images[0]->getOwnerId());
        $this->assertEquals('true', $images[0]->getAttribute('isPublic'));
        $this->assertEquals('available', $images[0]->getAttribute('imageState'));
    }
    /**
     * Test zones instance
     */
    public function testZonesInstance()
    {
        $zones = $this->infrastructure->zonesInstance();
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertTrue(is_array($zones));
        $this->assertEquals(4, count($zones));
        $this->assertEquals('us-east-1a', $zones[0][Instance::INSTANCE_ZONE]); 
        $this->assertEquals('us-east-1b', $zones[1][Instance::INSTANCE_ZONE]);
        $this->assertEquals('us-east-1c', $zones[2][Instance::INSTANCE_ZONE]);
        $this->assertEquals('us-east-1d', $zones[3][Instance::INSTANCE_ZONE]);
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
     * Test monitor instance
     */
    public function testMonitorInstance()
    {
        $monitor = $this->infrastructure->monitorInstance(self::SERVER_ID,Instance::MONITOR_CPU);
        $this->assertTrue($this->infrastructure->isSuccessful());
        $this->assertTrue(is_array($monitor));
        $this->assertTrue(is_float($monitor['average']));
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

if (PHPUnit_MAIN_METHOD == 'ZendTest\Cloud\Infrastructure\Adapter\Ec2OfflineTest::main') {
    Ec2OfflineTest::main();
}
