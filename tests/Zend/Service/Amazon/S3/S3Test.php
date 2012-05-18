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
class S3Test extends \PHPUnit_Framework_TestCase
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

        // Create a S3 instance
        $this->amazon  = new S3($accessKey, $secretKey);
        // Inject the stub into the application.
        $this->amazon->setHttpClient($this->httpClient);
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
}
