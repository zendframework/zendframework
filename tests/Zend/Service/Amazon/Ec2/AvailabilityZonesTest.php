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

use Zend\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2\Exception;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpClientTestAdapter;

/**
 * Zend\Service\Amazon\Ec2\Availabilityzones test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class AvailabilityZonesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Service\Amazon\Ec2\AvailabilityZones
     */
    protected $availabilityZones;

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
        $this->availabilityZones = new Ec2\AvailabilityZones('access_key', 'secret_access_key', null, $this->httpClient);
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
        $this->httpClientTestAdapter->setResponse($rawHttpResponse);

        $response = $this->availabilityZones->describe('us-east-1a');
        $this->assertInternalType('array', $response);
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
        $this->httpClientTestAdapter->setResponse($rawHttpResponse);

        $response = $this->availabilityZones->describe();

        $this->assertInternalType('array', $response);

        $arrExpected = array('us-east-1a', 'us-east-1b', 'us-east-1c');
        foreach ($response as $k => $node) {
            $this->assertEquals($arrExpected[$k], $node['zoneName']);
        }
    }
}

