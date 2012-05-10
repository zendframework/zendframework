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
 * Zend\Service\Amazon\Ec\Region test case.
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
class RegionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Availabilityzones
     */
    private $regionInstance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {$this->regionInstance = new Ec2\Region('access_key', 'secret_access_key');

        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\Region::setDefaultHTTPClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->Zend_Service_Amazon_Ec2_Availabilityzones = null;
    }

    public function testDescribeSingleRegion()
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
                    . "<DescribeRegionsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <regionInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <regionName>us-east-1</regionName>\r\n"
                    . "      <regionUrl>us-east-1.ec2.amazonaws.com</regionUrl>\r\n"
                    . "    </item>\r\n"
                    . "  </regionInfo>\r\n"
                    . "</DescribeRegionsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->regionInstance->describe('us-east-1');

        $arrRegion = array(
            array(
                'regionName'    => 'us-east-1',
                'regionUrl'     => 'us-east-1.ec2.amazonaws.com'
            )
        );

        $this->assertSame($arrRegion, $response);
    }

    public function testDescribeMultipleRegions()
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
                    . "<DescribeRegionsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <regionInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <regionName>us-east-1</regionName>\r\n"
                    . "      <regionUrl>us-east-1.ec2.amazonaws.com</regionUrl>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <regionName>us-west-1</regionName>\r\n"
                    . "      <regionUrl>us-west-1.ec2.amazonaws.com</regionUrl>\r\n"
                    . "    </item>\r\n"
                    . "  </regionInfo>\r\n"
                    . "</DescribeRegionsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->regionInstance->describe(array('us-east-1','us-west-1'));

        $arrRegion = array(
            array(
                'regionName'    => 'us-east-1',
                'regionUrl'     => 'us-east-1.ec2.amazonaws.com'
            ),
            array(
                'regionName'    => 'us-west-1',
                'regionUrl'     => 'us-west-1.ec2.amazonaws.com'
            )
        );

        $this->assertSame($arrRegion, $response);
    }
}

