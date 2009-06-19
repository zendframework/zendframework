<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'Zend/Service/Amazon/Ec2/Availabilityzones.php';

/**
 * Zend_Service_Amazon_Ec2_Availabilityzones test case.
 */
class Zend_Service_Amazon_Ec2_AvailabilityzonesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Availabilityzones
     */
    private $Zend_Service_Amazon_Ec2_Availabilityzones;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Ec2_Availabilityzones = new Zend_Service_Amazon_Ec2_Availabilityzones('access_key', 'secret_access_key');

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Amazon_Ec2_Availabilityzones::setHttpClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->Zend_Service_Amazon_Ec2_Availabilityzones = null;

        parent::tearDown();
    }

    public function testDescribeSingleAvailabilityZone()
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
                    . "<DescribeAvailabilityZonesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <availabilityZoneInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <zoneName>us-east-1a</zoneName>\r\n"
                    . "      <zoneState>available</zoneState>\r\n"
                    . "    </item>\r\n"
                    . "  </availabilityZoneInfo>\r\n"
                    . "</DescribeAvailabilityZonesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Availabilityzones->describe('us-east-1a');
        $this->assertType('array', $response);
        $this->assertEquals('us-east-1a', $response[0]['zoneName']);
        $this->assertEquals('available', $response[0]['zoneState']);
    }

    public function testDescribeMultipleAvailabilityZones()
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
                    . "<DescribeAvailabilityZonesResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <availabilityZoneInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <zoneName>us-east-1a</zoneName>\r\n"
                    . "      <zoneState>available</zoneState>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <zoneName>us-east-1b</zoneName>\r\n"
                    . "      <zoneState>available</zoneState>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <zoneName>us-east-1c</zoneName>\r\n"
                    . "      <zoneState>available</zoneState>\r\n"
                    . "    </item>\r\n"
                    . "  </availabilityZoneInfo>\r\n"
                    . "</DescribeAvailabilityZonesResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Availabilityzones->describe();

        $this->assertType('array', $response);

        $arrExpected = array('us-east-1a', 'us-east-1b', 'us-east-1c');
        foreach ($response as $k => $node) {
            $this->assertEquals($arrExpected[$k], $node['zoneName']);
        }
    }
}

