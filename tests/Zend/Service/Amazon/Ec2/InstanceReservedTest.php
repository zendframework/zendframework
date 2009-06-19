<?php

require_once 'Zend/Service/Amazon/Ec2/Instance/Reserved.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Service_Amazon_Ec2_Instance_Reserved test case.
 */
class InstanceReservedTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Instance_Reserved
     */
    private $Zend_Service_Amazon_Ec2_Instance_Reserved;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->Zend_Service_Amazon_Ec2_Instance_Reserved = new Zend_Service_Amazon_Ec2_Instance_Reserved('access_key', 'secret_access_key');

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Amazon_Ec2_Instance_Reserved::setHttpClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);
        $this->Zend_Service_Amazon_Ec2_Instance_Reserved = null;
        parent::tearDown();
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
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Instance_Reserved->describeInstances('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8');

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
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Instance_Reserved->describeOfferings();

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
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Instance_Reserved->purchaseOffering('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8');

        $this->assertSame('4b2293b4-5813-4cc8-9ce3-1957fc1dcfc8', $return);

    }

}

