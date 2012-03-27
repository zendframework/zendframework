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
use Zend\Service\Amazon\Ec2\WindowsInstance;

/**
 * Zend_Service_Amazon_Ec2_Instance_Windows test case.
 *
 * @todo: Should this class be named Zend_Service_Amazon_Ec2_Instance_WindowsTest?
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
class InstanceWindowsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Instance_Windows
     */
    private $instance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->instance = new WindowsInstance('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        WindowsInstance::setDefaultHTTPClient($client);
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

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Windows->bundle()
     */
    public function testBundle()
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
                    ."<BundleInstanceResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <requestId>bun-c1a540a8</requestId>\r\n"
                    ."  <bundleInstanceTask>\r\n"
                    ."      <instanceId>i-12345678</instanceId>\r\n"
                    ."      <bundleId>bun-cla322b9</bundleId>\r\n"
                    ."      <state>bundling</state>\r\n"
                    ."      <startTime>2008-10-07T11:41:50.000Z</startTime>\r\n"
                    ."      <updateTime>2008-10-07T11:51:50.000Z</updateTime>\r\n"
                    ."      <progress>20%</progress>\r\n"
                    ."      <storage>\r\n"
                    ."        <S3>\r\n"
                    ."          <bucket>my-bucket</bucket>\r\n"
                    ."          <prefix>my-new-image</prefix>\r\n"
                    ."        </S3>\r\n"
                    ."      </storage>\r\n"
                    ."  </bundleInstanceTask>\r\n"
                    ."</BundleInstanceResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->bundle('i-12345678', 'my-bucket', 'my-new-image');

        //print_r($return);

        $arrReturn = array(
                "instanceId" => "i-12345678",
                "bundleId" => "bun-cla322b9",
                "state" => "bundling",
                "startTime" => "2008-10-07T11:41:50.000Z",
                "updateTime" => "2008-10-07T11:51:50.000Z",
                "progress" => "20%",
                "storage" => array(
                        "s3" => array
                            (
                                "bucket" => "my-bucket",
                                "prefix" => "my-new-image"
                            )
                    )
                );

        $this->assertSame($arrReturn, $return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Windows->cancelBundle()
     */
    public function testCancelBundle()
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
                    ."<CancelBundleTaskResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <bundleInstanceTask>\r\n"
                    ."      <instanceId>i-12345678</instanceId>\r\n"
                    ."      <bundleId>bun-cla322b9</bundleId>\r\n"
                    ."      <state>canceling</state>\r\n"
                    ."      <startTime>2008-10-07T11:41:50.000Z</startTime>\r\n"
                    ."      <updateTime>2008-10-07T11:51:50.000Z</updateTime>\r\n"
                    ."      <progress>20%</progress>\r\n"
                    ."      <storage>\r\n"
                    ."        <S3>\r\n"
                    ."          <bucket>my-bucket</bucket>\r\n"
                    ."          <prefix>my-new-image</prefix>\r\n"
                    ."        </S3>\r\n"
                    ."      </storage>\r\n"
                    ."  </bundleInstanceTask>\r\n"
                    ."</CancelBundleTaskResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->cancelBundle('bun-cla322b9');

        $arrReturn = array(    "instanceId" => "i-12345678",
                "bundleId" => "bun-cla322b9",
                "state" => "canceling",
                "startTime" => "2008-10-07T11:41:50.000Z",
                "updateTime" => "2008-10-07T11:51:50.000Z",
                "progress" => "20%",
                "storage" => array(
                        "s3" => array
                            (
                                "bucket" => "my-bucket",
                                "prefix" => "my-new-image"
                            )
                    )
                );

        $this->assertSame($arrReturn, $return);



    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Windows->describeBundle()
     */
    public function testDescribeBundle()
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
                    ."<DescribeBundleTasksResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <bundleInstanceTasksSet>\r\n"
                    ."    <item>\r\n"
                    ."      <instanceId>i-12345678</instanceId>\r\n"
                    ."      <bundleId>bun-cla322b9</bundleId>\r\n"
                    ."      <state>bundling</state>\r\n"
                    ."      <startTime>2008-10-07T11:41:50.000Z</startTime>\r\n"
                    ."      <updateTime>2008-10-07T11:51:50.000Z</updateTime>\r\n"
                    ."      <progress>20%</progress>\r\n"
                    ."      <storage>\r\n"
                    ."        <S3>\r\n"
                    ."          <bucket>my-bucket</bucket>\r\n"
                    ."          <prefix>my-new-image</prefix>\r\n"
                    ."        </S3>\r\n"
                    ."      </storage>\r\n"
                    ."    </item>\r\n"
                    ."  </bundleInstanceTasksSet>\r\n"
                    ."</DescribeBundleTasksResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->instance->describeBundle('bun-cla322b9');

        $arrReturn = array(
            array(
                "instanceId" => "i-12345678",
                "bundleId" => "bun-cla322b9",
                "state" => "bundling",
                "startTime" => "2008-10-07T11:41:50.000Z",
                "updateTime" => "2008-10-07T11:51:50.000Z",
                "progress" => "20%",
                "storage" => array(
                        "s3" => array
                            (
                                "bucket" => "my-bucket",
                                "prefix" => "my-new-image"
                            )
                    )
                )
            );

        $this->assertSame($arrReturn, $return);

    }

}

