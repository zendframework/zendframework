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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2;

/**
 * Zend_Service_Amazon_Ec2_Keypair test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class KeypairTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Keypair
     */
    private $Zend_Service_Amazon_Ec2_Keypair;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Ec2_Keypair = new Ec2\Keypair('access_key', 'secret_access_key');

        $adapter = new \Zend\HTTP\Client\Adapter\Test();
        $client = new \Zend\HTTP\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\Keypair::setDefaultHTTPClient($client);


    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->Zend_Service_Amazon_Ec2_Keypair = null;

        parent::tearDown();
    }

    public function testCreateKeyPairNoNameThrowsException()
    {
        try {
            $this->Zend_Service_Amazon_Ec2_Keypair->create('');
            $this->fail('An exception should be thrown if an empty keyname is passed in.');
        } catch (Ec2\Exception $zsaee) {
        }
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Keypair->create()
     */
    public function testCreateKeyPair()
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
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<CreateKeyPairResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "    <keyName>example-key-name</keyName>\r\n"
                    . "    <keyFingerprint>1f:51:ae:28:bf:89:e9:d8:1f:25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f</keyFingerprint>\r\n"
                    . "    <keyMaterial>-----BEGIN RSA PRIVATE KEY-----\r\n"
. "MIIEoQIBAAKCAQBuLFg5ujHrtm1jnutSuoO8Xe56LlT+HM8v/xkaa39EstM3/aFxTHgElQiJLChp\r\n"
. "HungXQ29VTc8rc1bW0lkdi23OH5eqkMHGhvEwqa0HWASUMll4o3o/IX+0f2UcPoKCOVUR+jx71Sg\r\n"
. "5AU52EQfanIn3ZQ8lFW7Edp5a3q4DhjGlUKToHVbicL5E+g45zfB95wIyywWZfeW/UUF3LpGZyq/\r\n"
. "ebIUlq1qTbHkLbCC2r7RTn8vpQWp47BGVYGtGSBMpTRP5hnbzzuqj3itkiLHjU39S2sJCJ0TrJx5\r\n"
. "i8BygR4s3mHKBj8l+ePQxG1kGbF6R4yg6sECmXn17MRQVXODNHZbAgMBAAECggEAY1tsiUsIwDl5\r\n"
. "91CXirkYGuVfLyLflXenxfI50mDFms/mumTqloHO7tr0oriHDR5K7wMcY/YY5YkcXNo7mvUVD1pM\r\n"
. "ZNUJs7rw9gZRTrf7LylaJ58kOcyajw8TsC4e4LPbFaHwS1d6K8rXh64o6WgW4SrsB6ICmr1kGQI7\r\n"
. "3wcfgt5ecIu4TZf0OE9IHjn+2eRlsrjBdeORi7KiUNC/pAG23I6MdDOFEQRcCSigCj+4/mciFUSA\r\n"
. "SWS4dMbrpb9FNSIcf9dcLxVM7/6KxgJNfZc9XWzUw77Jg8x92Zd0fVhHOux5IZC+UvSKWB4dyfcI\r\n"
. "tE8C3p9bbU9VGyY5vLCAiIb4qQKBgQDLiO24GXrIkswF32YtBBMuVgLGCwU9h9HlO9mKAc2m8Cm1\r\n"
. "jUE5IpzRjTedc9I2qiIMUTwtgnw42auSCzbUeYMURPtDqyQ7p6AjMujp9EPemcSVOK9vXYL0Ptco\r\n"
. "xW9MC0dtV6iPkCN7gOqiZXPRKaFbWADp16p8UAIvS/a5XXk5jwKBgQCKkpHi2EISh1uRkhxljyWC\r\n"
. "iDCiK6JBRsMvpLbc0v5dKwP5alo1fmdR5PJaV2qvZSj5CYNpMAy1/EDNTY5OSIJU+0KFmQbyhsbm\r\n"
. "rdLNLDL4+TcnT7c62/aH01ohYaf/VCbRhtLlBfqGoQc7+sAc8vmKkesnF7CqCEKDyF/dhrxYdQKB\r\n"
. "gC0iZzzNAapayz1+JcVTwwEid6j9JqNXbBc+Z2YwMi+T0Fv/P/hwkX/ypeOXnIUcw0Ih/YtGBVAC\r\n"
. "DQbsz7LcY1HqXiHKYNWNvXgwwO+oiChjxvEkSdsTTIfnK4VSCvU9BxDbQHjdiNDJbL6oar92UN7V\r\n"
. "rBYvChJZF7LvUH4YmVpHAoGAbZ2X7XvoeEO+uZ58/BGKOIGHByHBDiXtzMhdJr15HTYjxK7OgTZm\r\n"
. "gK+8zp4L9IbvLGDMJO8vft32XPEWuvI8twCzFH+CsWLQADZMZKSsBasOZ/h1FwhdMgCMcY+Qlzd4\r\n"
. "JZKjTSu3i7vhvx6RzdSedXEMNTZWN4qlIx3kR5aHcukCgYA9T+Zrvm1F0seQPbLknn7EqhXIjBaT\r\n"
. "P8TTvW/6bdPi23ExzxZn7KOdrfclYRph1LHMpAONv/x2xALIf91UB+v5ohy1oDoasL0gij1houRe\r\n"
. "2ERKKdwz0ZL9SWq6VTdhr/5G994CK72fy5WhyERbDjUIdHaK3M849JJuf8cSrvSb4g==\r\n"
. "-----END RSA PRIVATE KEY-----</keyMaterial>\r\n"
                    . "</CreateKeyPairResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Keypair->create('example-key-name');

        $this->assertType('array', $response);

        $this->assertEquals('example-key-name', $response['keyName']);
        $this->assertEquals('1f:51:ae:28:bf:89:e9:d8:1f:25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f', $response['keyFingerprint']);
    }

    public function testDescribeSingleKeyPair()
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
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<DescribeKeyPairsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <keySet>\r\n"
                    . "    <item>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "      <keyFingerprint>1f:51:ae:28:bf:89:e9:d8:1f:25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f</keyFingerprint>\r\n"
                    . "    </item>\r\n"
                    . "  </keySet>\r\n"
                    . "</DescribeKeyPairsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Keypair->describe('example-key-name');
        $this->assertEquals('example-key-name', $response[0]['keyName']);
    }

    public function testDescribeMultipleKeyPair()
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
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<DescribeKeyPairsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <keySet>\r\n"
                    . "    <item>\r\n"
                    . "      <keyName>example-key-name</keyName>\r\n"
                    . "      <keyFingerprint>1f:51:ae:28:bf:89:e9:d8:1f:25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f</keyFingerprint>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <keyName>zend-test-key</keyName>\r\n"
                    . "      <keyFingerprint>25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f:1f:51:ae:28:bf:89:e9:d8:1f</keyFingerprint>\r\n"
                    . "    </item>\r\n"
                    . "  </keySet>\r\n"
                    . "</DescribeKeyPairsResponse>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Keypair->describe(array('example-key-name', 'zend-test-key'));

        $arrKeys = array(
            array(
                'keyName'       => 'example-key-name',
                'keyFingerprint'=> '1f:51:ae:28:bf:89:e9:d8:1f:25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f'
            ),
            array(
                'keyName'       => 'zend-test-key',
                'keyFingerprint'=> '25:5d:37:2d:7d:b8:ca:9f:f5:f1:6f:1f:51:ae:28:bf:89:e9:d8:1f'
            )
        );

        foreach($response as $k => $r) {
            $this->assertSame($arrKeys[$k], $r);
        }
    }

    public function testDeleteKeyPairNoNameThrowsException()
    {
        try {
            $this->Zend_Service_Amazon_Ec2_Keypair->delete('');
            $this->fail('An exception should be thrown if an empty keyname is passed in.');
        } catch (Ec2\Exception $zsaee) {
        }
    }

    public function testDeleteFailsOnNonValidKey()
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
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<DeleteKeyPair xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>false</return>\r\n"
                    . "</DeleteKeyPair>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Keypair->delete('myfakekeyname');
        $this->assertType('boolean', $response);
        $this->assertFalse($response);
    }

    public function testDeleteDoesNotFailOnValidKey()
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
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<DeleteKeyPair xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</DeleteKeyPair>";
        $this->adapter->setResponse($rawHttpResponse);

        $response = $this->Zend_Service_Amazon_Ec2_Keypair->delete('example-key-name');
        $this->assertType('boolean', $response);
        $this->assertTrue($response);
    }
}

