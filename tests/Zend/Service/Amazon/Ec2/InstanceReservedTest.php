<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Amazon\Ec2;

use Zend\Service\Amazon\Ec2\ReservedInstance;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpClientTestAdapter;

/**
 * Zend_Service_Amazon_Ec2_Instance_Reserved test case.
 * @todo Should this class be named Zend_Service_Amazon_Ec2_Instance_ReservedTest?
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class InstanceReservedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Service\Amazon\Ec2\ReservedInstance
     */
    private $instance;

    /**
     * @var HttpClient
     */
    protected $httpClient = null;

    /**
     * @var HttpClientTestAdapter
     */
    protected $httpClientTestAdapter = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->httpClientTestAdapter = new HttpClientTestAdapter;
        $this->httpClient = new HttpClient(null, array('adapter' => $this->httpClientTestAdapter));

        $this->instance = new ReservedInstance('access_key', 'secret_access_key', null, $this->httpClient);
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Reserved->describeInstances()
     */
    public function testDescribeInstances()
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
                    ."<DescribeReservedInstancesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <reservedInstancesSet>\r\n"
                    ."    <item>\r\n"
                    ."      <reservedInstancesId>4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8</reservedInstancesId>\r\n"
                    ."      <instanceType>m1.small</instanceType>\r\n"
                    ."      <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    ."      <duration>12</duration>\r\n"
                    ."      <usagePrice>0.00</usagePrice>\r\n"
                    ."      <fixedPrice>0.00</fixedPrice>\r\n"
                    ."      <instanceCount>19</instanceCount>\r\n"
                    ."      <productDescription>m1.small offering in us-east-1a</productDescription>\r\n"
                    ."      <state>Active</state>\r\n"
                    ."    </item>\r\n"
                    ."  </reservedInstancesSet>\r\n"
                    ."</DescribeReservedInstancesResponse>";
        $this->httpClientTestAdapter->setResponse($rawHttpResponse);

        $return = $this->instance->describeInstances('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8');

        $arrReturn = array(
            array(
            "reservedInstancesId" => "4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8",
            "instanceType" => "m1.small",
            "availabilityZone" => "us-east-1a",
            "duration" => "12",
            "fixedPrice" => "0.00",
            "usagePrice" => "0.00",
            "productDescription" => "m1.small offering in us-east-1a",
            "instanceCount" => "19",
            "state" => "Active"
            )
        );

        $this->assertSame($arrReturn, $return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Reserved->describeOfferings()
     */
    public function testDescribeOfferings()
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
                    ."<DescribeReservedInstancesOfferingsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <reservedInstancesOfferingsSet>\r\n"
                    ."    <item>\r\n"
                    ."      <reservedInstancesOfferingId>4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8</reservedInstancesOfferingId>\r\n"
                    ."      <instanceType>m1.small</instanceType>\r\n"
                    ."      <availabilityZone>us-east-1a</availabilityZone>\r\n"
                    ."      <duration>12</duration>\r\n"
                    ."      <usagePrice>0.00</usagePrice>\r\n"
                    ."      <fixedPrice>0.00</fixedPrice>\r\n"
                    ."      <productDescription>m1.small offering in us-east-1a</productDescription>\r\n"
                    ."    </item>\r\n"
                    ."  </reservedInstancesOfferingsSet>\r\n"
                    ."</DescribeReservedInstancesOfferingsResponse>";
        $this->httpClientTestAdapter->setResponse($rawHttpResponse);

        $return = $this->instance->describeOfferings();

        $arrReturn = array(
            array(
            "reservedInstancesOfferingId" => "4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8",
            "instanceType" => "m1.small",
            "availabilityZone" => "us-east-1a",
            "duration" => "12",
            "fixedPrice" => "0.00",
            "usagePrice" => "0.00",
            "productDescription" => "m1.small offering in us-east-1a",
            )
        );

        $this->assertSame($arrReturn, $return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Instance_Reserved->purchaseOffering()
     */
    public function testPurchaseOffering()
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
                    ."<PurchaseReservedInstancesOfferingResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    ."  <reservedInstancesId>4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8</reservedInstancesId>\r\n"
                    ."</PurchaseReservedInstancesOfferingResponse>";
        $this->httpClientTestAdapter->setResponse($rawHttpResponse);

        $return = $this->instance->purchaseOffering('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8');

        $this->assertSame('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8', $return);

    }

}

