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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2\Instance,
    Zend\Service\Amazon\Ec2\Exception;

/**
 * Zend_Service_Amazon_Ec2_Instance test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class InstanceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Instance
     */
    private $instance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = new Instance('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Instance::setDefaultHTTPClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->instance = null;

        parent::tearDown();
    }

    public function testConstants()
    {
        $this->assertEquals('m1.small', Instance::SMALL);
        $this->assertEquals('m1.large', Instance::LARGE);
        $this->assertEquals('m1.xlarge', Instance::XLARGE);
        $this->assertEquals('c1.medium', Instance::HCPU_MEDIUM);
        $this->assertEquals('c1.xlarge', Instance::HCPU_XLARGE);
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance->confirmProduct()
     */
    public function testConfirmProductReturnsOwnerId()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<ConfirmProductInstanceResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <result>true</result>\r\n"
                    . "  <ownerId>254933287430</ownerId>\r\n"
                    . "</ConfirmProductInstanceResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->confirmProduct('254933287430', 'i-1bda7172');

        $this->assertEquals('254933287430', $return['ownerId']);
    }

    public function testConfirmProductReturnsFalse()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<ConfirmProductInstanceResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <result>false</result>\r\n"
                    . "</ConfirmProductInstanceResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->confirmProduct('254933287430', 'i-1bda7172');

        $this->assertFalse($return);
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance->describe()
     */
    public function testDescribeSingleInstance()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <reservationSet>\r\n"
                    . "    <item>\r\n"
                    . "      <reservationId>r-44a5402d</reservationId>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupSet>\r\n"
                    . "        <item>\r\n"
                    . "          <groupId>default</groupId>\r\n"
                    . "        </item>\r\n"
                    . "      </groupSet>\r\n"
                    . "      <instancesSet>\r\n"
                    . "        <item>\r\n"
                    . "          <instanceId>i-28a64341</instanceId>\r\n"
                    . "          <imageId>ami-6ea54007</imageId>\r\n"
                    . "          <instanceState>\r\n"
                    . "            <code>0</code>\r\n"
                    . "            <name>running</name>\r\n"
                    . "          </instanceState>\r\n"
                    . "          <privateDnsName>10-251-50-75.ec2.internal</privateDnsName>\r\n"
                    . "          <dnsName>ec2-72-44-33-4.compute-1.amazonaws.com</dnsName>\r\n"
                    . "          <keyName>example-key-name</keyName>\r\n"
                    . "          <productCodesSet>\r\n"
                    . "            <item><productCode>774F4FF8</productCode></item>\r\n"
                    . "          </productCodesSet>\r\n"
                    . "          <instanceType>m1.small</instanceType>\r\n"
                    . "          <launchTime>2007-08-07T11:54:42.000Z</launchTime>\r\n"
                    . "          <placement>\r\n"
                    . "           <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "          </placement>\r\n"
                    . "          <kernelId>aki-ba3adfd3</kernelId>\r\n"
                    . "          <ramdiskId>ari-badbad00</ramdiskId>\r\n"
                    . "        </item>\r\n"
                    . "      </instancesSet>\r\n"
                    . "    </item>\r\n"
                    . "  </reservationSet>\r\n"
                    . "</DescribeInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->describe('i-28a64341');

        $this->assertEquals('r-44a5402d', $return['instances'][0]['reservationId']);
        $this->assertEquals('default', $return['instances'][0]['groupSet'][0]);
        $this->assertEquals('i-28a64341', $return['instances'][0]['instanceId']);
        $this->assertEquals('ami-6ea54007', $return['instances'][0]['imageId']);
        $this->assertEquals('m1.small', $return['instances'][0]['instanceType']);
        $this->assertEquals('us-east-1b', $return['instances'][0]['availabilityZone']);
    }

    public function testDescribeIgnoreTerminatedInstance()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <reservationSet>\r\n"
                    . "    <item>\r\n"
                    . "      <reservationId>r-44a5402d</reservationId>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupSet>\r\n"
                    . "        <item>\r\n"
                    . "          <groupId>default</groupId>\r\n"
                    . "        </item>\r\n"
                    . "      </groupSet>\r\n"
                    . "      <instancesSet>\r\n"
                    . "        <item>\r\n"
                    . "          <instanceId>i-28a64341</instanceId>\r\n"
                    . "          <imageId>ami-6ea54007</imageId>\r\n"
                    . "          <instanceState>\r\n"
                    . "            <code>48</code>\r\n"
                    . "            <name>terminated</name>\r\n"
                    . "          </instanceState>\r\n"
                    . "          <privateDnsName>10-251-50-75.ec2.internal</privateDnsName>\r\n"
                    . "          <dnsName>ec2-72-44-33-4.compute-1.amazonaws.com</dnsName>\r\n"
                    . "          <keyName>example-key-name</keyName>\r\n"
                    . "          <productCodesSet>\r\n"
                    . "            <item><productCode>774F4FF8</productCode></item>\r\n"
                    . "          </productCodesSet>\r\n"
                    . "          <instanceType>m1.small</instanceType>\r\n"
                    . "          <launchTime>2007-08-07T11:54:42.000Z</launchTime>\r\n"
                    . "          <placement>\r\n"
                    . "           <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "          </placement>\r\n"
                    . "          <kernelId>aki-ba3adfd3</kernelId>\r\n"
                    . "          <ramdiskId>ari-badbad00</ramdiskId>\r\n"
                    . "        </item>\r\n"
                    . "      </instancesSet>\r\n"
                    . "    </item>\r\n"
                    . "  </reservationSet>\r\n"
                    . "</DescribeInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse, true);

        $return = $this->instance->describe('i-28a64341', true);

        $this->assertEquals(0, count($return['instances']));
    }

    public function testDescribeByImageId()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <reservationSet>\r\n"
                    . "    <item>\r\n"
                    . "      <reservationId>r-44a5402d</reservationId>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupSet>\r\n"
                    . "        <item>\r\n"
                    . "          <groupId>default</groupId>\r\n"
                    . "        </item>\r\n"
                    . "      </groupSet>\r\n"
                    . "      <instancesSet>\r\n"
                    . "        <item>\r\n"
                    . "          <instanceId>i-28a64341</instanceId>\r\n"
                    . "          <imageId>ami-6ea54007</imageId>\r\n"
                    . "          <instanceState>\r\n"
                    . "            <code>0</code>\r\n"
                    . "            <name>running</name>\r\n"
                    . "          </instanceState>\r\n"
                    . "          <privateDnsName>10-251-50-75.ec2.internal</privateDnsName>\r\n"
                    . "          <dnsName>ec2-72-44-33-4.compute-1.amazonaws.com</dnsName>\r\n"
                    . "          <keyName>example-key-name</keyName>\r\n"
                    . "          <productCodesSet>\r\n"
                    . "            <item><productCode>774F4FF8</productCode></item>\r\n"
                    . "          </productCodesSet>\r\n"
                    . "          <instanceType>m1.small</instanceType>\r\n"
                    . "          <launchTime>2007-08-07T11:54:42.000Z</launchTime>\r\n"
                    . "          <placement>\r\n"
                    . "           <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "          </placement>\r\n"
                    . "          <kernelId>aki-ba3adfd3</kernelId>\r\n"
                    . "          <ramdiskId>ari-badbad00</ramdiskId>\r\n"
                    . "        </item>\r\n"
                    . "      </instancesSet>\r\n"
                    . "    </item>\r\n"
                    . "  </reservationSet>\r\n"
                    . "</DescribeInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->describeByImageId('ami-6ea54007');

        $this->assertEquals('i-28a64341', $return[0]['instanceId']);
        $this->assertEquals('ami-6ea54007', $return[0]['imageId']);
        $this->assertEquals('m1.small', $return[0]['instanceType']);
        $this->assertEquals('us-east-1b', $return[0]['availabilityZone']);
    }

    public function testRunThrowsExceptionWhenNoImageIdPassedIn()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Ec2\Exception\InvalidArgumentException',
            'No Image Id Provided');
        $arrStart = array(
            'maxStart' => 3,
            'keyName'   => 'example-key-name',
            'securityGroup'    => 'default',
            'userData'          => 'instance_id=www3',
            'placement'         => 'us-east-1b',
            'kernelId'          => 'aki-4438dd2d',
            'ramdiskId'         => 'ari-4538dd2c',
            'blockDeviceVirtualName'    => 'vertdevice',
            'blockDeviceName'       => '/dev/sdv'
        );

        $return = $this->instance->run($arrStart);
    }

    public function testRunOneSecurityGroup()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<RunInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <reservationId>r-47a5402e</reservationId>\r\n"
                    . "  <ownerId>495219933132</ownerId>\r\n"
                    . "  <groupSet>\r\n"
                    . "    <item>\r\n"
                    . "      <groupId>default</groupId>\r\n"
                    . "    </item>\r\n"
                    . "  </groupSet>\r\n"
                    . "  <instancesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-2ba64342</instanceId>\r\n"
                    . "      <imageId>ami-60a54009</imageId>\r\n"
                    . "      <instanceState>\r\n"
                    . "        <code>0</code>\r\n"
                    . "        <name>pending</name>\r\n"
                    . "      </instanceState>\r\n"
                    . "      <privateDnsName></privateDnsName>\r\n"
                    . "      <dnsName></dnsName>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "       <amiLaunchIndex>0</amiLaunchIndex>\r\n"
                    . "      <InstanceType>m1.small</InstanceType>\r\n"
                    . "      <launchTime>2007-08-07T11:51:50.000Z</launchTime>\r\n"
                    . "      <placement>\r\n"
                    . "        <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "      </placement>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-2bc64242</instanceId>\r\n"
                    . "      <imageId>ami-60a54009</imageId>\r\n"
                    . "      <instanceState>\r\n"
                    . "        <code>0</code>\r\n"
                    . "        <name>pending</name>\r\n"
                    . "      </instanceState>\r\n"
                    . "      <privateDnsName></privateDnsName>\r\n"
                    . "      <dnsName></dnsName>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "      <amiLaunchIndex>1</amiLaunchIndex>\r\n"
                    . "      <InstanceType>m1.small</InstanceType>\r\n"
                    . "      <launchTime>2007-08-07T11:51:50.000Z</launchTime>\r\n"
                    . "      <placement>\r\n"
                    . "        <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "      </placement>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-2be64332</instanceId>\r\n"
                    . "      <imageId>ami-60a54009</imageId>\r\n"
                    . "      <instanceState>\r\n"
                    . "        <code>0</code>\r\n"
                    . "        <name>pending</name>\r\n"
                    . "      </instanceState>\r\n"
                    . "      <privateDnsName></privateDnsName>\r\n"
                    . "      <dnsName></dnsName>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "      <amiLaunchIndex>2</amiLaunchIndex>\r\n"
                    . "      <InstanceType>m1.small</InstanceType>\r\n"
                    . "      <launchTime>2007-08-07T11:51:50.000Z</launchTime>\r\n"
                    . "      <placement>\r\n"
                    . "        <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "      </placement>\r\n"
                    . "    </item>\r\n"
                    . "  </instancesSet>\r\n"
                    . "</RunInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);


        $arrStart = array(
            'imageId' => 'ami-60a54009',
            'maxStart' => 3,
            'keyName'   => 'example-key-name',
            'securityGroup'    => 'default',
            'userData'          => 'instance_id=www3',
            'placement'         => 'us-east-1b',
            'kernelId'          => 'aki-4438dd2d',
            'ramdiskId'         => 'ari-4538dd2c',
            'blockDeviceVirtualName'    => 'vertdevice',
            'blockDeviceName'       => '/dev/sdv'
        );

        $return = $this->instance->run($arrStart);

        $this->assertEquals(3, count($return['instances']));
        $this->assertEquals('495219933132', $return['ownerId']);

        $arrInstanceIds = array('i-2ba64342', 'i-2bc64242', 'i-2be64332');

        foreach($return['instances'] as $k => $r) {
            $this->assertEquals($arrInstanceIds[$k], $r['instanceId']);
            $this->assertEquals($k, $r['amiLaunchIndex']);
        }

    }

    public function testRunMultipleSecurityGroups()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\nn"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<RunInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <reservationId>r-47a5402e</reservationId>\r\n"
                    . "  <ownerId>495219933132</ownerId>\r\n"
                    . "  <groupSet>\r\n"
                    . "    <item>\r\n"
                    . "      <groupId>default</groupId>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <groupId>web</groupId>\r\n"
                    . "    </item>\r\n"
                    . "  </groupSet>\r\n"
                    . "  <instancesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-2ba64342</instanceId>\r\n"
                    . "      <imageId>ami-60a54009</imageId>\r\n"
                    . "      <instanceState>\r\n"
                    . "        <code>0</code>\r\n"
                    . "        <name>pending</name>\r\n"
                    . "      </instanceState>\r\n"
                    . "      <privateDnsName></privateDnsName>\r\n"
                    . "      <dnsName></dnsName>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "       <amiLaunchIndex>0</amiLaunchIndex>\r\n"
                    . "      <InstanceType>m1.small</InstanceType>\r\n"
                    . "      <launchTime>2007-08-07T11:51:50.000Z</launchTime>\r\n"
                    . "      <placement>\r\n"
                    . "        <availabilityZone>us-east-1b</availabilityZone>\r\n"
                    . "      </placement>\r\n"
                    . "    </item>\r\n"
                    . "  </instancesSet>\r\n"
                    . "</RunInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $arrStart = array(
            'imageId' => 'ami-60a54009',
            'keyName'   => 'example-key-name',
            'securityGroup'    => array('default','web'),
            'userData'          => 'instance_id=www3',
            'placement'         => 'us-east-1b',
            'kernelId'          => 'aki-4438dd2d',
            'ramdiskId'         => 'ari-4538dd2c',
            'blockDeviceVirtualName'    => 'vertdevice',
            'blockDeviceName'       => '/dev/sdv'
        );

        $return = $this->instance->run($arrStart);

        $arrGroups = array('default', 'web');

        $this->assertSame($arrGroups, $return['groupSet']);
    }

    public function testTerminateSingleInstances()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<TerminateInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <instancesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-28a64341</instanceId>\r\n"
                    . "      <shutdownState>\r\n"
                    . "        <code>32</code>\r\n"
                    . "        <name>shutting-down</name>\r\n"
                    . "      </shutdownState>\r\n"
                    . "      <previousState>\r\n"
                    . "        <code>16</code>\r\n"
                    . "        <name>running</name>\r\n"
                    . "      </previousState>\r\n"
                    . "    </item>\r\n"
                    . "  </instancesSet>\r\n"
                    . "</TerminateInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->terminate('i-28a64341');

        $this->assertEquals(1, count($return));

        foreach($return as $r) {
            $this->assertEquals('i-28a64341', $r['instanceId']);
        }
    }

    public function testTerminateMultipleInstances()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<TerminateInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <instancesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-28a64341</instanceId>\r\n"
                    . "      <shutdownState>\r\n"
                    . "        <code>32</code>\r\n"
                    . "        <name>shutting-down</name>\r\n"
                    . "      </shutdownState>\r\n"
                    . "      <previousState>\r\n"
                    . "        <code>16</code>\r\n"
                    . "        <name>running</name>\r\n"
                    . "      </previousState>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <instanceId>i-21a64348</instanceId>\r\n"
                    . "      <shutdownState>\r\n"
                    . "        <code>32</code>\r\n"
                    . "        <name>shutting-down</name>\r\n"
                    . "      </shutdownState>\r\n"
                    . "      <previousState>\r\n"
                    . "        <code>16</code>\r\n"
                    . "        <name>running</name>\r\n"
                    . "      </previousState>\r\n"
                    . "    </item>\r\n"
                    . "  </instancesSet>\r\n"
                    . "</TerminateInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $arrInstanceIds = array('i-28a64341', 'i-21a64348');

        $return = $this->instance->terminate($arrInstanceIds);

        $this->assertEquals(2, count($return));

        foreach($return as $k=>$r) {
            $this->assertEquals($arrInstanceIds[$k], $r['instanceId']);
        }
    }

    public function testRebootMultipleInstances()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<RebootInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</RebootInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $arrInstanceIds = array('i-28a64341', 'i-21a64348');
        $return = $this->instance->reboot($arrInstanceIds);

        $this->assertTrue($return);
    }

    public function testRebootSingleInstance()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<RebootInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</RebootInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->reboot('i-28a64341');

        $this->assertTrue($return);
    }

    public function testGetConsoleOutput()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<GetConsoleOutputResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <instanceId>i-28a64341</instanceId>\r\n"
                    . "  <timestamp>2007-01-03 15:00:00</timestamp>\r\n"
                    . "  <output>TGludXggdmVyc2lvbiAyLjYuMTYteGVuVSAoYnVpbGRlckBwYXRjaGJhdC5hbWF6b25zYSkgKGdj\r\n"
. "YyB2ZXJzaW9uIDQuMC4xIDIwMDUwNzI3IChSZWQgSGF0IDQuMC4xLTUpKSAjMSBTTVAgVGh1IE9j\r\n"
. "dCAyNiAwODo0MToyNiBTQVNUIDIwMDYKQklPUy1wcm92aWRlZCBwaHlzaWNhbCBSQU0gbWFwOgpY\r\n"
. "ZW46IDAwMDAwMDAwMDAwMDAwMDAgLSAwMDAwMDAwMDZhNDAwMDAwICh1c2FibGUpCjk4ME1CIEhJ\r\n"
. "R0hNRU0gYXZhaWxhYmxlLgo3MjdNQiBMT1dNRU0gYXZhaWxhYmxlLgpOWCAoRXhlY3V0ZSBEaXNh\r\n"
. "YmxlKSBwcm90ZWN0aW9uOiBhY3RpdmUKSVJRIGxvY2t1cCBkZXRlY3Rpb24gZGlzYWJsZWQKQnVp\r\n"
. "bHQgMSB6b25lbGlzdHMKS2VybmVsIGNvbW1hbmQgbGluZTogcm9vdD0vZGV2L3NkYTEgcm8gNApF\r\n"
. "bmFibGluZyBmYXN0IEZQVSBzYXZlIGFuZCByZXN0b3JlLi4uIGRvbmUuCg==</output>\r\n"
                    . "</GetConsoleOutputResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->consoleOutput('i-28a64341');

        $arrOutput = array(
            'instanceId'    => 'i-28a64341',
            'timestamp'     => '2007-01-03 15:00:00',
            'output'        => "Linux version 2.6.16-xenU (builder@patchbat.amazonsa) (gcc version 4.0.1 20050727 (Red Hat 4.0.1-5)) #1 SMP Thu Oct 26 08:41:26 SAST 2006\n"
. "BIOS-provided physical RAM map:\n"
. "Xen: 0000000000000000 - 000000006a400000 (usable)\n"
. "980MB HIGHMEM available.\n"
. "727MB LOWMEM available.\n"
. "NX (Execute Disable) protection: active\n"
. "IRQ lockup detection disabled\n"
. "Built 1 zonelists\n"
. "Kernel command line: root=/dev/sda1 ro 4\n"
. "Enabling fast FPU save and restore... done.\n");

        $this->assertSame($arrOutput, $return);
    }

    public function testMonitorInstance()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<MonitorInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <instancesSet>"
                    . "    <item>"
                    . "      <instanceId>i-43a4412a</instanceId>"
                    . "      <monitoring>"
                    . "        <state>monitoring</state>"
                    . "      </monitoring>"
                    . "    </item>"
                    . "  </instancesSet>"
                    . "</MonitorInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->monitor('i-43a4412a');

        $arrReturn = array(array('instanceid' => 'i-43a4412a', 'monitorstate' => 'monitoring'));
        $this->assertSame($arrReturn, $return);
    }

    public function testUnmonitorInstance()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<UnmonitorInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <instancesSet>"
                    . "    <item>"
                    . "      <instanceId>i-43a4412a</instanceId>"
                    . "      <monitoring>"
                    . "        <state>pending</state>"
                    . "      </monitoring>"
                    . "    </item>"
                    . "  </instancesSet>"
                    . "</UnmonitorInstancesResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->unmonitor('i-43a4412a');

        $arrReturn = array(array('instanceid' => 'i-43a4412a', 'monitorstate' => 'pending'));
        $this->assertSame($arrReturn, $return);
    }

}

