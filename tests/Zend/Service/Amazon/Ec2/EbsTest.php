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

namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2;

/**
 * Zend\Service\Amazon\Ec2\Ebs test case.
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
class EbsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Service\Amazon\Ec2\Ebs
     */
    private $ebsInstance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->ebsInstance = new Ec2\Ebs('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\Ebs::setDefaultHTTPClient($client);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);
        $this->ebsInstance = null;
    }

    public function testAttachVolume()
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
                    . "<AttachVolumeResponse  xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <volumeId>vol-4d826724</volumeId>\r\n"
                    . "  <instanceId>i-6058a509</instanceId>\r\n"
                    . "  <device>/dev/sdh</device>\r\n"
                    . "  <status>attaching</status>\r\n"
                    . "  <attachTime>2008-05-07T11:51:50.000Z</attachTime>\r\n"
                    . "</AttachVolumeResponse >";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->attachVolume('vol-4d826724', 'i-6058a509', '/dev/sdh');

        $arrAttach = array(
            'volumeId'  => 'vol-4d826724',
            'instanceId'  => 'i-6058a509',
            'device'  => '/dev/sdh',
            'status'  => 'attaching',
            'attachTime'  => '2008-05-07T11:51:50.000Z'
        );

        $this->assertSame($arrAttach, $return);
    }

    public function testCreateSnapshot()
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
                    . "<CreateSnapshotResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <snapshotId>snap-78a54011</snapshotId>\r\n"
                    . "  <volumeId>vol-4d826724</volumeId>\r\n"
                    . "  <status>pending</status>\r\n"
                    . "  <startTime>2008-05-07T11:51:50.000Z</startTime>\r\n"
                    . "  <progress></progress>\r\n"
                    . "</CreateSnapshotResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->createSnapshot('vol-4d826724');

        $arrCreateSnapShot = array(
            'snapshotId'  => 'snap-78a54011',
            'volumeId'  => 'vol-4d826724',
            'status'  => 'pending',
            'startTime'  => '2008-05-07T11:51:50.000Z',
            'progress'  => ''
        );

        $this->assertSame($arrCreateSnapShot, $return);

    }

    public function testCreateNewVolume()
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
                    . "<CreateVolumeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <volumeId>vol-4d826724</volumeId>\r\n"
                    . "  <size>400</size>\r\n"
                    . "  <status>creating</status>\r\n"
                    . "  <createTime>2008-05-07T11:51:50.000Z</createTime>\r\n"
                    . "  <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    . "  <snapshotId></snapshotId>\r\n"
                    . "</CreateVolumeResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->createNewVolume(400, 'us-east-1a');

        $arrCreateNewVolume = array(
            'volumeId'  => 'vol-4d826724',
            'size'  => '400',
            'status'  => 'creating',
            'createTime'  => '2008-05-07T11:51:50.000Z',
            'availabilityZone'  => 'us-east-1a'
        );

        $this->assertSame($arrCreateNewVolume, $return);

    }

    public function testCreateVolumeFromSnapshot()
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
                    . "<CreateVolumeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <volumeId>vol-4d826724</volumeId>\r\n"
                    . "  <size>400</size>\r\n"
                    . "  <status>creating</status>\r\n"
                    . "  <createTime>2008-05-07T11:51:50.000Z</createTime>\r\n"
                    . "  <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    . "  <snapshotId>snap-78a54011</snapshotId>\r\n"
                    . "</CreateVolumeResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->createVolumeFromSnapshot('snap-78a54011', 'us-east-1a');

        $arrCreateNewVolume = array(
            'volumeId'  => 'vol-4d826724',
            'size'  => '400',
            'status'  => 'creating',
            'createTime'  => '2008-05-07T11:51:50.000Z',
            'availabilityZone'  => 'us-east-1a',
            'snapshotId'        => 'snap-78a54011'
        );

        $this->assertSame($arrCreateNewVolume, $return);

    }

    public function testDeleteSnapshot()
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
                    . "<DeleteSnapshotResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</DeleteSnapshotResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->deleteSnapshot('snap-78a54011');

        $this->assertTrue($return);

    }

    public function testDeleteVolume()
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
                    . "<DeleteVolumeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</DeleteVolumeResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->deleteVolume('vol-4d826724');

        $this->assertTrue($return);
    }

    /**
     * Tests Zend\Service\Amazon\Ec2\Ebs->describeSnapshot()
     */
    public function testDescribeSingleSnapshot()
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
                    . "<DescribeSnapshotsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <snapshotSet>\r\n"
                    . "    <item>\r\n"
                    . "      <snapshotId>snap-78a54011</snapshotId>\r\n"
                    . "      <volumeId>vol-4d826724</volumeId>\r\n"
                    . "      <status>pending</status>\r\n"
                    . "      <startTime>2008-05-07T12:51:50.000Z</startTime>\r\n"
                    . "      <progress>80%</progress>\r\n"
                    . "    </item>\r\n"
                    . "  </snapshotSet>\r\n"
                    . "</DescribeSnapshotsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->describeSnapshot('snap-78a54011');

        $arrSnapshot = array(array(
            'snapshotId'        => 'snap-78a54011',
            'volumeId'  => 'vol-4d826724',
            'status'  => 'pending',
            'startTime'  => '2008-05-07T12:51:50.000Z',
            'progress'  => '80%'
        ));

        $this->assertSame($arrSnapshot, $return);


    }

    public function testDescribeMultipleSnapshots()
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
                    . "<DescribeSnapshotsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <snapshotSet>\r\n"
                    . "    <item>\r\n"
                    . "      <snapshotId>snap-78a54011</snapshotId>\r\n"
                    . "      <volumeId>vol-4d826724</volumeId>\r\n"
                    . "      <status>pending</status>\r\n"
                    . "      <startTime>2008-05-07T12:51:50.000Z</startTime>\r\n"
                    . "      <progress>80%</progress>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <snapshotId>snap-78a54012</snapshotId>\r\n"
                    . "      <volumeId>vol-4d826725</volumeId>\r\n"
                    . "      <status>pending</status>\r\n"
                    . "      <startTime>2008-08-07T12:51:50.000Z</startTime>\r\n"
                    . "      <progress>65%</progress>\r\n"
                    . "    </item>\r\n"
                    . "  </snapshotSet>\r\n"
                    . "</DescribeSnapshotsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->describeSnapshot(array('snap-78a54011', 'snap-78a54012'));

        $arrSnapshots = array(
            array(
                'snapshotId'    => 'snap-78a54011',
                'volumeId'      => 'vol-4d826724',
                'status'        => 'pending',
                'startTime'     => '2008-05-07T12:51:50.000Z',
                'progress'      => '80%',
            ),
            array(
                'snapshotId'    => 'snap-78a54012',
                'volumeId'      => 'vol-4d826725',
                'status'        => 'pending',
                'startTime'     => '2008-08-07T12:51:50.000Z',
                'progress'      => '65%',
            )
        );

        $this->assertSame($arrSnapshots, $return);

    }

    /**
     * Tests Zend\Service\Amazon\Ec2\Ebs->describeVolume()
     */
    public function testDescribeSingleVolume()
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
                    . "<DescribeVolumesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "<volumeSet>\r\n"
                    . "  <item>\r\n"
                    . "    <volumeId>vol-4282672b</volumeId>\r\n"
                    . "    <size>800</size>\r\n"
                    . "    <status>in-use</status>\r\n"
                    . "    <createTime>2008-05-07T11:51:50.000Z</createTime>\r\n"
                    . "    <attachmentSet>\r\n"
                    . "      <item>\r\n"
                    . "        <volumeId>vol-4282672b</volumeId>\r\n"
                    . "        <instanceId>i-6058a509</instanceId>\r\n"
                    . "        <device>/dev/sdh</device>\r\n"
                    . "        <snapshotId>snap-12345678</snapshotId>\r\n"
                    . "        <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    . "        <status>attached</status>\r\n"
                    . "        <attachTime>2008-05-07T12:51:50.000Z</attachTime>\r\n"
                    . "      </item>\r\n"
                    . "    </attachmentSet>\r\n"
                    . "  </item>\r\n"
                    . "</volumeSet>\r\n"
                    . "</DescribeVolumesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->describeVolume('vol-4282672b');

        $arrVolumes = array(
            array(
                'volumeId'          => 'vol-4282672b',
                'size'              => '800',
                'status'            => 'in-use',
                'createTime'        => '2008-05-07T11:51:50.000Z',
                'attachmentSet'     => array(
                    'volumeId'              => 'vol-4282672b',
                    'instanceId'            => 'i-6058a509',
                    'device'                => '/dev/sdh',
                    'status'                => 'attached',
                    'attachTime'            => '2008-05-07T12:51:50.000Z',
                )
            )
        );

        $this->assertSame($arrVolumes, $return);

    }

    public function testDescribeMultipleVolume()
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
                    . "<DescribeVolumesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "<volumeSet>\r\n"
                    . "  <item>\r\n"
                    . "    <volumeId>vol-4282672b</volumeId>\r\n"
                    . "    <size>800</size>\r\n"
                    . "    <status>in-use</status>\r\n"
                    . "    <createTime>2008-05-07T11:51:50.000Z</createTime>\r\n"
                    . "    <attachmentSet>\r\n"
                    . "      <item>\r\n"
                    . "        <volumeId>vol-4282672b</volumeId>\r\n"
                    . "        <instanceId>i-6058a509</instanceId>\r\n"
                    . "        <device>/dev/sdh</device>\r\n"
                    . "        <snapshotId>snap-12345678</snapshotId>\r\n"
                    . "        <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    . "        <status>attached</status>\r\n"
                    . "        <attachTime>2008-05-07T12:51:50.000Z</attachTime>\r\n"
                    . "      </item>\r\n"
                    . "    </attachmentSet>\r\n"
                    . "  </item>\r\n"
                    . "  <item>\r\n"
                    . "    <volumeId>vol-42826775</volumeId>\r\n"
                    . "    <size>40</size>\r\n"
                    . "    <status>available</status>\r\n"
                    . "    <createTime>2008-08-07T11:51:50.000Z</createTime>\r\n"
                    . "  </item>\r\n"
                    . "</volumeSet>\r\n"
                    . "</DescribeVolumesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->describeVolume(array('vol-4282672b', 'vol-42826775'));

        $arrVolumes = array(
            array(
                'volumeId'          => 'vol-4282672b',
                'size'              => '800',
                'status'            => 'in-use',
                'createTime'        => '2008-05-07T11:51:50.000Z',
                'attachmentSet'     => array(
                    'volumeId'              => 'vol-4282672b',
                    'instanceId'            => 'i-6058a509',
                    'device'                => '/dev/sdh',
                    'status'                => 'attached',
                    'attachTime'            => '2008-05-07T12:51:50.000Z',
                )
            ),
            array(
                'volumeId'          => 'vol-42826775',
                'size'              => '40',
                'status'            => 'available',
                'createTime'        => '2008-08-07T11:51:50.000Z'
            )
        );

        $this->assertSame($arrVolumes, $return);
    }

    public function testDescribeAttachedVolumes()
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
                    . "<DescribeVolumesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "<volumeSet>\r\n"
                    . "  <item>\r\n"
                    . "    <volumeId>vol-4282672b</volumeId>\r\n"
                    . "    <size>800</size>\r\n"
                    . "    <status>in-use</status>\r\n"
                    . "    <createTime>2008-05-07T11:51:50.000Z</createTime>\r\n"
                    . "    <attachmentSet>\r\n"
                    . "      <item>\r\n"
                    . "        <volumeId>vol-4282672b</volumeId>\r\n"
                    . "        <instanceId>i-6058a509</instanceId>\r\n"
                    . "        <device>/dev/sdh</device>\r\n"
                    . "        <snapshotId>snap-12345678</snapshotId>\r\n"
                    . "        <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    . "        <status>attached</status>\r\n"
                    . "        <attachTime>2008-05-07T12:51:50.000Z</attachTime>\r\n"
                    . "      </item>\r\n"
                    . "    </attachmentSet>\r\n"
                    . "  </item>\r\n"
                    . "  <item>\r\n"
                    . "    <volumeId>vol-42826775</volumeId>\r\n"
                    . "    <size>40</size>\r\n"
                    . "    <status>available</status>\r\n"
                    . "    <createTime>2008-08-07T11:51:50.000Z</createTime>\r\n"
                    . "  </item>\r\n"
                    . "</volumeSet>\r\n"
                    . "</DescribeVolumesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->describeAttachedVolumes('i-6058a509');

        $arrVolumes = array(
            array(
                'volumeId'          => 'vol-4282672b',
                'size'              => '800',
                'status'            => 'in-use',
                'createTime'        => '2008-05-07T11:51:50.000Z',
                'attachmentSet'     => array(
                    'volumeId'              => 'vol-4282672b',
                    'instanceId'            => 'i-6058a509',
                    'device'                => '/dev/sdh',
                    'status'                => 'attached',
                    'attachTime'            => '2008-05-07T12:51:50.000Z',
                )
            )
        );

        $this->assertSame($arrVolumes, $return);
    }

    /**
     * Tests Zend\Service\Amazon\Ec2\Ebs->detachVolume()
     */
    public function testDetachVolume()
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
                    . "<DetachVolumeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <volumeId>vol-4d826724</volumeId>\r\n"
                    . "  <instanceId>i-6058a509</instanceId>\r\n"
                    . "  <device>/dev/sdh</device>\r\n"
                    . "  <status>detaching</status>\r\n"
                    . "  <attachTime>2008-05-08T11:51:50.000Z</attachTime>\r\n"
                    . "</DetachVolumeResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ebsInstance->detachVolume('vol-4d826724');

        $arrVolume = array(
            'volumeId'      => 'vol-4d826724',
            'instanceId'    => 'i-6058a509',
            'device'        => '/dev/sdh',
            'status'        => 'detaching',
            'attachTime'    => '2008-05-08T11:51:50.000Z'
        );

        $this->assertSame($arrVolume, $return);
    }

}

