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
use Zend\Service\Amazon\Ec2, 
    Zend\Service\Amazon\Ec2\Exception;

/**
 * Zend\Service\Amazon\Ec2\Image test case.
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
class ImageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Service\Amazon\Ec2\Image
     */
    private $ec2ImageInstance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->ec2ImageInstance = new Ec2\Image('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\Image::setDefaultHTTPClient($client);
    }

    protected function tearDown()
    {
        $this->ec2ImageInstance = null;

        parent::tearDown();
    }

    public function testDeregister()
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
                    . "<DeregisterImageResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</DeregisterImageResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->deregister('ami-61a54008');

        $this->assertTrue($return);

    }

    public function testDescribeSingleImageMultipleImagesByIds()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd6</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>true</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe(array('ami-be3adfd7', 'ami-be3adfd6'));

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            ),
            array(
                'imageId'   => 'ami-be3adfd6',
                'imageLocation'   => 'ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'true',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeSingleImageById()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe('ami-be3adfd7');

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeSingleImageMultipleImagesByOwnerId()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>2060296256884</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd6</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>true</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe(null, array('2060296256884', '206029621532'));

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '2060296256884',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            ),
            array(
                'imageId'   => 'ami-be3adfd6',
                'imageLocation'   => 'ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'true',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeSingleImageByOwnerId()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe(null, '206029621532');

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeSingleImageMultipleImagesByExecutableBy()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>2060296256884</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd6</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>true</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe(null, null, array('46361432890', '432432265322'));

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '2060296256884',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            ),
            array(
                'imageId'   => 'ami-be3adfd6',
                'imageLocation'   => 'ec2-public-images/ubuntu-8.10-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'true',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeSingleImageByExecutableBy()
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
                    . "<DescribeImagesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imagesSet>\r\n"
                    . "    <item>\r\n"
                    . "      <imageId>ami-be3adfd7</imageId>\r\n"
                    . "      <imageLocation>ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml</imageLocation>\r\n"
                    . "      <imageState>available</imageState>\r\n"
                    . "      <imageOwnerId>206029621532</imageOwnerId>\r\n"
                    . "      <isPublic>false</isPublic>\r\n"
                    . "      <architecture>i386</architecture>\r\n"
                    . "      <imageType>machine</imageType>\r\n"
                    . "      <kernelId>aki-4438dd2d</kernelId>\r\n"
                    . "      <ramdiskId>ari-4538dd2c</ramdiskId>\r\n"
                    . "    </item>\r\n"
                    . "  </imagesSet>\r\n"
                    . "</DescribeImagesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describe(null, null, '46361432890');

        $arrImage = array(
            array(
                'imageId'   => 'ami-be3adfd7',
                'imageLocation'   => 'ec2-public-images/fedora-8-i386-base-v1.04.manifest.xml',
                'imageState'   => 'available',
                'imageOwnerId'   => '206029621532',
                'isPublic'   => 'false',
                'architecture'   => 'i386',
                'imageType'   => 'machine',
                'kernelId'   => 'aki-4438dd2d',
                'ramdiskId'   => 'ari-4538dd2c',
                'platform'   => '',
            )
        );

        $this->assertSame($arrImage, $return);
    }

    public function testDescribeAttributeLaunchPermission()
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
                    . "<DescribeImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imageId>ami-61a54008</imageId>\r\n"
                    . "  <launchPermission>\r\n"
                    . "    <item>\r\n"
                    . "      <userId>495219933132</userId>\r\n"
                    . "    </item>\r\n"
                    . "  </launchPermission>\r\n"
                    . "</DescribeImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describeAttribute('ami-61a54008', 'launchPermission');

        $this->assertEquals('ami-61a54008', $return['imageId']);
        $this->assertEquals('495219933132', $return['launchPermission'][0]);
    }

    public function testDescribeAttributeProductCodes()
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
                    . "<DescribeImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imageId>ami-61a54008</imageId>\r\n"
                    . "  <productCodes>\r\n"
                    . "    <item>\r\n"
                    . "      <productCode>774F4FF8</productCode>\r\n"
                    . "    </item>\r\n"
                    . "  </productCodes>\r\n"
                    . "</DescribeImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->describeAttribute('ami-61a54008', 'productCodes');

        $this->assertEquals('ami-61a54008', $return['imageId']);
        $this->assertEquals('774F4FF8', $return['productCodes'][0]);
    }

    public function testModifyAttributeSingleLaunchPermission()
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
                    . "<ModifyImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</ModifyImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->modifyAttribute('ami-61a54008', 'launchPermission', 'add', '495219933132', 'all');
        $this->assertTrue($return);
    }

    public function testModifyAttributeMultipleLaunchPermission()
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
                    . "<ModifyImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</ModifyImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->modifyAttribute('ami-61a54008', 'launchPermission', 'add', array('495219933132', '495219933133'), array('all', 'all'));
        $this->assertTrue($return);
    }

    public function testModifyAttributeThrowsExceptionOnInvalidAttribute()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Ec2\Exception\InvalidArgumentException', 
            'Invalid attribute passed in.');
        $return = $this->ec2ImageInstance->modifyAttribute('ami-61a54008', 'invalidPermission', 'add', '495219933132', 'all');
    }

    public function testModifyAttributeProuctCodes()
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
                    . "<ModifyImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</ModifyImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->modifyAttribute('ami-61a54008', 'productCodes', null, null, null, '774F4FF8');

        $this->assertTrue($return);

    }

    public function testRegister()
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
                    . "<RegisterImageResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <imageId>ami-61a54008</imageId>\r\n"
                    . "</RegisterImageResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->register('mybucket-myimage.manifest.xml');

        $this->assertEquals('ami-61a54008', $return);

    }

    public function testResetAttribute()
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
                    . "<ResetImageAttributeResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</ResetImageAttributeResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->ec2ImageInstance->resetAttribute('ami-61a54008', 'launchPermission');

        $this->assertTrue($return);

    }

}

