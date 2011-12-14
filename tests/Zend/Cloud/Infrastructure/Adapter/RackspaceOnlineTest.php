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
    Zend\Cloud\Infrastructure\Adapter\Rackspace,
    Zend\Http\Client\Adapter\Socket,
    Zend\Cloud\Infrastructure\Instance;

class RackspaceOnlineTest extends \PHPUnit_Framework_TestCase
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
     * Setup for each test
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend\Cloud\Infrastructure\Adapter\Rackspace online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER') || !defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY')) {
            $this->markTestSkipped('Constants User and Key have to be set.');
        }

        self::$infrastructure = \Zend\Cloud\Infrastructure\Factory::getAdapter(array(
            \Zend\Cloud\Infrastructure\Factory::INFRASTRUCTURE_ADAPTER_KEY => 'Zend\Cloud\Infrastructure\Adapter\Rackspace',
            \Zend\Cloud\Infrastructure\Adapter\Rackspace::RACKSPACE_USER   => constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER'),
            \Zend\Cloud\Infrastructure\Adapter\Rackspace::RACKSPACE_KEY    => constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY'),
            \Zend\Cloud\Infrastructure\Adapter\Rackspace::RACKSPACE_REGION => constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_REGION')
        ));

        self::$httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();


        self::$infrastructure->getAdapter()
                             ->getHttpClient()
                             ->setAdapter(self::$httpClientAdapterSocket);
        // terms of use compliance: no more than two queries per second
        sleep(2);
    }

    /**
     * Test all the constants of the class
     */
    public function testConstants()
    {
        $this->assertEquals('rackspace_user', Rackspace::RACKSPACE_USER);
        $this->assertEquals('rackspace_key', Rackspace::RACKSPACE_KEY);
        $this->assertEquals('rackspace_region', Rackspace::RACKSPACE_REGION);
        $this->assertEquals('USA',Rackspace::RACKSPACE_ZONE_USA);
        $this->assertEquals('UK',Rackspace::RACKSPACE_ZONE_UK);
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
     * Test getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertInstanceOf('Zend\Service\Rackspace\Servers',self::$infrastructure->getAdapter());
    }
    /**
     * Test create an instance
     */
    public function testCreateInstance()
    {
        $options = array (
            'imageId'  => constant('TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGEID'),
            'flavorId' => constant('TESTS_ZEND_SERVICE_RACKSPACE_SERVER_FLAVORID'),
            'metadata' => array (
                'foo' => 'bar'
            )
        );
        $instance = self::$infrastructure->createInstance(constant('TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGE_NAME'), $options);
        self::$instanceId= $instance->getId();
        $this->assertEquals(constant('TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGEID'), $instance->getImageId());
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
        $this->assertTrue(!empty($instances));
    }
    /**
     * Test images instance
     */
    public function testImagesInstance()
    {
        $images = self::$infrastructure->imagesInstance();
        $this->assertTrue(!empty($images));
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
        if (self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_RUNNING)) {
            $this->assertTrue(self::$infrastructure->rebootInstance(self::$instanceId));
        } else {
             $this->markTestSkipped('I cannot reboot the instance because is not in the running state');
        }
    }
    /**
     * Test destroy instance
     */
    public function testDestroyInstance()
    {
        $this->assertTrue(self::$infrastructure->WaitStatusInstance(self::$instanceId, Instance::STATUS_RUNNING));
        $this->assertTrue(self::$infrastructure->destroyInstance(self::$instanceId));
    }
}


/**
 * @category   Zend
 * @package    Zend\Cloud\Infrastructure\Adapter\Rackspace
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Cloud\Infrastructure
 * @group      Zend\Cloud\Infrastructure\Adapter\Rackspace
 */
class SkipRackspaceOnlineTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend\Cloud\Infrastructure\Adapter\Rackspace online tests not enabled in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
