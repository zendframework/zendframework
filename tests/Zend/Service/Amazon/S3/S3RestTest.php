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
 * @package    Zend_Service_Amazon_S3
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Amazon\S3;

use DateTime;
use Zend\Service\Amazon\S3\S3;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon_S3
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_S3
 */
class S3RestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var S3
     */
    protected $amazon;

    /**
     * Http Client stub
     *
     * @var \Zend\Http\Client
     */
    protected $httpClient;

    /**
     * Uri Http stub
     *
     * @var \Zend\Uri\Http
     */
    protected $uriHttp;

    /**
     * Http Response stub
     *
     * @var \Zend\Http\Response
     */
    protected $httpResponse;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        
        $accessKey = 'accessKey';
        $secretKey = 'secretKey';

        // Create a stub for Http Client
        $this->httpClient = $this->getMockBuilder('Zend\Http\Client')->getMock();
        // Create a stub for Http response for be used later.
        $this->httpResponse = $this->getMockBuilder('Zend\Http\Response')->getMock();
        // Create a stub for Uri\Http for later.
        $this->uriHttp = $this->getMockBuilder('Zend\Uri\Http')->getMock();

        $this->uriHttp->expects($this->any())
                      ->method('isValid')
                      ->will($this->returnValue(true));


        // Create a S3 instance
        $this->amazon  = new S3($accessKey, $secretKey);
        // Inject the stub into the application.
        $this->amazon->setHttpClient($this->httpClient);
        // Inject the stub into the application.
        $this->amazon->setEndpoint($this->uriHttp);
    }

    /**
     * Test get buckets
     *
     * @return void
     */
    public function testGetBuckets()
    {
        $expected  = array('quotes', 'samples');

        // Http Response results
        $this->httpResponse->expects($this->any())
                           ->method('getStatusCode')
                           ->will($this->returnValue(200));
        $rawBody = <<<BODY
<?xml version="1.0" encoding="UTF-8"?>
<ListAllMyBucketsResult xmlns="http://doc.s3.amazonaws.com/2006-03-01">
    <Owner>
        <ID>bcaf1ffd86f461ca5fb16fd081034f</ID>
        <DisplayName>webfile</DisplayName>
    </Owner>
    <Buckets>
        <Bucket>
            <Name>quotes</Name>
            <CreationDate>2006-02-03T16:45:09.000Z</CreationDate>
        </Bucket>
        <Bucket>
            <Name>samples</Name>
            <CreationDate>2006-02-03T16:41:58.000Z</CreationDate>
        </Bucket>
    </Buckets>
</ListAllMyBucketsResult>
BODY;
        $this->httpResponse->expects($this->any())
                           ->method('getBody')
                           ->will($this->returnValue($rawBody));

        // Expects to be called only once the method send() then return a Http Response.
        $this->httpClient->expects($this->once())
                         ->method('send')
                         ->will($this->returnValue($this->httpResponse));

        $buckets = $this->amazon->getBuckets();

        $this->assertEquals($expected, $buckets);
    }

    /**
     * Test create bucket
     *
     * @return void
     */
    public function testCreateBuckets()
    {
        //Valid bucket name
        $bucket   = 'iamavalidbucket';
        $location = '';
        $requestDate = DateTime::createFromFormat(DateTime::RFC1123, 'Tue, 15 May 2012 15:18:31 +0000');
        $this->amazon->setRequestDate($requestDate);
        $this->amazon->setKeys('AKIAIDCZ2WXN6NNB7YZA', 'sagA0Lge8R+ifORcyb6Z/qVbmtimFCUczvh51Jq8'); //Fake keys

        /**
         * Check of request inside _makeRequest
         * 
         */
        $this->uriHttp->expects($this->once())
            ->method('getHost')
            ->with()
            ->will($this->returnValue('s3.amazonaws.com'));

        $this->uriHttp->expects($this->once())
            ->method('setHost')
            ->with('iamavalidbucket.s3.amazonaws.com');

        $this->uriHttp->expects($this->once())
            ->method('setPath')
            ->with('/');

        $this->httpClient->expects($this->once())
             ->method('setUri')
            ->with($this->uriHttp);  

        $this->httpClient->expects($this->once())
            ->method('setMethod')
            ->with('PUT');

        $this->httpClient->expects($this->once())
            ->method('setHeaders')
            ->with(array(
                    "Date"          => "Tue, 15 May 2012 15:18:31 +0000",
                    "Content-Type"  => "application/xml",
                    "Authorization" => "AWS AKIAIDCZ2WXN6NNB7YZA:NjNlNGY4OWQ5YzQ4ZDcwMDYyZDU4OWY1MDRjOWNlMmJkMDgzODhlMw==",
                    ));

        /**
         * Fake response inside _makeRequest
         *
         */
        // Http Response results
        $this->httpResponse->expects($this->any())
              ->method('getStatusCode')
              ->will($this->returnValue(200));

        // Expects to be called only once the method send() then return a Http Response.
        $this->httpClient->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->httpResponse));

        $response = $this->amazon->createBucket($bucket, $location);

        $this->assertTrue($response);
    }

    /**
     * Test valid bucket name
     *
     * @return void
     */
    public function testValidBucketName()
    {

        $this->assertTrue($this->amazon->_validBucketName('iam.avalid.1bucket-name.endingwithnumber9'));
    }

    /**
     * Test invalid bucket name (name too short)
     *
     * @return void
     */
    public function testBucketNameShort()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('ia');
    }

    /**
     * Test invalid bucket name (name too long)
     *
     * @return void
     */
    public function testBucketNameLong()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('iam.aninvalid.bucketname.because.iam.way.tooooooooooooooooo.long');
    }

    /**
     * Test invalid bucket name (capital letters)
     *
     * @return void
     */
    public function testBucketNameCapitalLetter()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('iam.anInvalid.bucketname');
    }

    /**
     * Test invalid bucket name (ip address)
     *
     * @return void
     */
    public function testBucketNameIpAddress()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('1.0.255.90');
    }

    /**
     * Test invalid bucket name (empty label)
     *
     * @return void
     */
    public function testBucketNameLabelEmtpy()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('iam.aninvalid..empty.bucketname');
    }

    /**
     * Test invalid bucket name (label starting with dash)
     *
     * @return void
     */
    public function testBucketNameLabelDash()
    {
        $this->setExpectedException('\Zend\Service\Amazon\S3\Exception\InvalidArgumentException');
        $this->amazon->_validBucketName('iam.aninvalid.-bucketname');
    }
}

