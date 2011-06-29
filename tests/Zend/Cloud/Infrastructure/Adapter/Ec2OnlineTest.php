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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Cloud\Infrastructure\Adapter;

use Zend\Cloud\Infrastructure\Adapter,
    Zend\Cloud\Infrastructure\Adapter\Ec2,
    Zend\Http\Client\Adapter\Socket,
    Zend\Cloud\Infrastructure\Instance;

class Ec2OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Timeout in seconds for status change
     */
    const STATUS_TIMEOUT= 60;
    /**
     * Reference to Infrastructure object
     *
     * @var Zend\Cloud\Infrastructure\Adapter
     */
    protected static $infrastructure;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend\Http\Client\Adapter\Socket
     */
    protected static $httpClientAdapterSocket;
    
    /**
     * Image ID of the instance
     * 
     * @var string
     */
    protected static $instanceId;
    
    /**
     * Sets up this test case
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            self::markTestSkipped('Zend\Cloud\Infrastructure\Adapter\Ec2 online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID') || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')) {
            self::markTestSkipped('Constants AccessKeyId and SecretKey have to be set.');
        }

        self::$infrastructure = \Zend\Cloud\Infrastructure\Factory::getAdapter(array( 
            \Zend\Cloud\Infrastructure\Factory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Ec2', 
            \Zend\Cloud\Infrastructure\Adapter\Ec2::AWS_ACCESS_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'), 
            \Zend\Cloud\Infrastructure\Adapter\Ec2::AWS_SECRET_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'), 
            \Zend\Cloud\Infrastructure\Adapter\Ec2::AWS_REGION     => constant('TESTS_ZEND_SERVICE_AMAZON_EC2_ZONE')   
        )); 

        self::$httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();


        self::$infrastructure->getAdapter()
                             ->getHttpClient()
                             ->setAdapter(self::$httpClientAdapterSocket);

    }
    /**
     * Setup for each test
     */
    public function setUp()
    {
        // terms of use compliance: no more than one query per second
        sleep(1);
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
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Instance',self::$infrastructure->getAdapter());
    }
    /**
     * Test create an instance
     */
    public function testCreateInstance()
    {
        $options = array (
            Instance::INSTANCE_IMAGEID => constant('TESTS_ZEND_SERVICE_AMAZON_EC2_IMAGE_ID'),
            Ec2::AWS_SECURITY_GROUP => array(constant('TESTS_ZEND_SERVICE_AMAZON_EC2_SECURITY_GROUP'))
        );
        $instance = self::$infrastructure->createInstance('test', $options);
        self::$instanceId= $instance->getId();
        $this->assertEquals(constant('TESTS_ZEND_SERVICE_AMAZON_EC2_IMAGE_ID'), $instance->getImageId());
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_RUNNING));
    }
    /**
     * Test last HTTP request
     */
    public function testGetLastHttpRequest()
    {
        $lastHttpRequest = self::$infrastructure->getLastHttpRequest();
        $this->assertTrue(!empty($lastHttpRequest));
    }
    /**
     * Test last HTTP response
     */
    public function testGetLastHttpResponse()
    {
        $lastHttpResponse = self::$infrastructure->getLastHttpResponse();
        $this->assertTrue(!empty($lastHttpResponse));
    }
    /**
     * Test adapter result
     */
    public function testGetAdapterResult()
    {
        $adapterResult = self::$infrastructure->getAdapterResult();
        $this->assertTrue(!empty($adapterResult));
    }
    /**
     * Test list of an instance
     */
    public function testListInstance()
    {
        $instances = self::$infrastructure->listInstances(self::$instanceId);
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->getId()==self::$instanceId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        unset($instances);
    }
    /**
     * Test images instance
     */
    public function testImagesInstance()
    {
        $images = self::$infrastructure->imagesInstance();
        $this->assertTrue(!empty($images));
        unset($images);
    }
    /**
     * Test zones instance
     */
    public function testZonesInstance()
    {
        $zones = self::$infrastructure->zonesInstance();
        $this->assertTrue(!empty($zones));
    }
    /**
     * Test monitor instance
     */
    public function testMonitorInstance()
    {
        $monitor= self::$infrastructure->monitorInstance(self::$instanceId,Instance::MONITOR_CPU);
        $adapterResult= self::$infrastructure->getAdapterResult();
        $this->assertTrue(!empty($adapterResult['label']));
        unset($monitor);
    }
    /**
     * Test stop an instance
     */
    public function testStopInstance()
    {
        self::$infrastructure->stopInstance(self::$instanceId);
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_STOPPED));
    }
    /**
     * Test start an instance
     */
    public function testStartInstance()
    {
        self::$infrastructure->startInstance(self::$instanceId);
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_RUNNING));
    }
    /**
     * Test reboot and instance
     */
    public function testRebootInstance()
    {
        $this->assertTrue(self::$infrastructure->rebootInstance(self::$instanceId));
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_RUNNING));
    }
    public function testDestroyInstance()
    {
        $this->assertTrue(self::$infrastructure->destroyInstance(self::$instanceId));
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_TERMINATED));
    }
}


/**
 * @category   Zend
 * @package    Zend\Cloud\INfrastructure\Adapter\Ec2
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Cloud\Infrastructure
 * @group      Zend\Cloud\Infrastructure\Adapter\Ec2
 */
class Skip extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend\Cloud\Infrastructure\Adapter\Ec2 online tests not enabled with an access key ID in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}

