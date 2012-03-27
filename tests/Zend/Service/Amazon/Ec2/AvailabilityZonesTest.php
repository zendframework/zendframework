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
 * Zend\Service\Amazon\Ec2\Availabilityzones test case.
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
class AvailabilityZonesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Service\Amazon\Ec2\Availabilityzones
     */
    private $availabilityZones;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->availabilityZones = new Ec2\AvailabilityZones('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\AvailabilityZones::setDefaultHttpClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->availabilityZones = null;

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
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->availabilityZones->describe();

        $this->assertInternalType('array', $response);

        $arrExpected = array('us-east-1a', 'us-east-1b', 'us-east-1c');
        foreach ($response as $k => $node) {
            $this->assertEquals($arrExpected[$k], $node['zoneName']);
        }
    }
}

