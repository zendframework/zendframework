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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'Zend/Service/Amazon/Ec2/Region.php';

/**
 * Zend_Service_Amazon_Ec2_Region test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Zend_Service_Amazon_Ec2_RegionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Availabilityzones
     */
    private $Zend_Service_Amazon_Ec2_Region;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Ec2_Region = new Zend_Service_Amazon_Ec2_Region('access_key', 'secret_access_key');

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Amazon_Ec2_Region::setHttpClient($client);

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

        $response = $this->Zend_Service_Amazon_Ec2_Region->describe('us-east-1');

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

        $response = $this->Zend_Service_Amazon_Ec2_Region->describe(array('us-east-1','us-west-1'));

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

