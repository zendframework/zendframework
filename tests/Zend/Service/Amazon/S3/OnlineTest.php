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

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\S3;

use Zend\Service\Amazon\S3,
    Zend\Service\Amazon\S3\Exception,
    Zend\Http\Response;

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
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend_Service_Amazon_S3
     */
    protected $_amazon;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Amazon_S3 online tests are not enabled');
        }
        $this->_amazon = new S3\S3(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
                                                    constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
                                                    );
        $this->_nosuchbucket = "nonexistingbucketnamewhichnobodyshoulduse";
        $this->_httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->_bucket = constant('TESTS_ZEND_SERVICE_AMAZON_S3_BUCKET');

        $this->_amazon->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);

        // terms of use compliance: no more than one query per second
        sleep(1);
    }

    /**
     * Test creating bucket
     *
     * @return void
     */
    public function testCreateBucket()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->assertTrue($this->_amazon->isBucketAvailable($this->_bucket));
        $list = $this->_amazon->getBuckets();
        $this->assertContains($this->_bucket, $list);
    }

    /**
     * Test creating bucket with location
     * ZF-6728
     *
     */
    public function testCreateBucketEU()
    {
        // make sure that we use different bucket,
        // as sometimes delete operation fails to propagate in one zone before you
        // attempt to recreate it in another
        $this->_bucket = $this->_bucket . 'eu';
        $this->_amazon->createBucket($this->_bucket, 'EU');
        $this->assertTrue($this->_amazon->isBucketAvailable($this->_bucket));
        $list = $this->_amazon->getBuckets();
        $this->assertContains($this->_bucket, $list);
    }

    /**
     * Test creating object
     *
     * @return void
     */
    public function testCreateObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket."/zftest", "testdata");
        $this->assertEquals("testdata", $this->_amazon->getObject($this->_bucket."/zftest"));
    }

    /**
     * Get object using streaming and temp files
     *
     */
    public function testGetObjectStream()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket."/zftest", "testdata");
        $response = $this->_amazon->getObjectStream($this->_bucket."/zftest");

        $this->assertTrue($response instanceof Response\Stream, 'The test did not return stream response');
        $this->assertTrue(is_resource($response->getStream()), 'Request does not contain stream!');

        $stream_name = $response->getStreamName();

        $stream_read = stream_get_contents($response->getStream());
        $file_read = file_get_contents($stream_name);

        $this->assertEquals("testdata", $stream_read, 'Downloaded stream does not seem to match!');
        $this->assertEquals("testdata", $file_read, 'Downloaded file does not seem to match!');
    }

    /**
     * Get object using streaming and specific files
     *
     */
    public function testGetObjectStreamNamed()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket."/zftest", "testdata");
        $outfile = tempnam(sys_get_temp_dir(), "output");

        $response = $this->_amazon->getObjectStream($this->_bucket."/zftest", $outfile);

        $this->assertTrue($response instanceof Response\Stream, 'The test did not return stream response');
        $this->assertTrue(is_resource($response->getStream()), 'Request does not contain stream!');

        $this->assertEquals($outfile, $response->getStreamName());

        $stream_read = stream_get_contents($response->getStream());
        $file_read = file_get_contents($outfile);

        $this->assertEquals("testdata", $stream_read, 'Downloaded stream does not seem to match!');
        $this->assertEquals("testdata", $file_read, 'Downloaded file does not seem to match!');
    }
/**
     * Test getting info
     *
     * @return void
     */
    public function testGetInfo()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $info = $this->_amazon->getInfo($this->_bucket."/zftest");
        $this->assertEquals('"'.md5($data).'"', $info["etag"]);
        $this->assertEquals(strlen($data), $info["size"]);

        $this->_amazon->putObject($this->_bucket."/zftest.jpg", $data, null);
        $info = $this->_amazon->getInfo($this->_bucket."/zftest.jpg");
        $this->assertEquals( 'image/jpeg', $info["type"]);
    }

    public function testNoBucket()
    {
        $this->assertFalse($this->_amazon->putObject($this->_nosuchbucket."/zftest", "testdata"));
        $this->assertFalse($this->_amazon->getObject($this->_nosuchbucket."/zftest"));
        $this->assertFalse($this->_amazon->getObjectsByBucket($this->_nosuchbucket));
    }

    public function testNoObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->assertFalse($this->_amazon->getObject($this->_bucket."/zftest-no-such-object/in/there"));
        $this->assertFalse($this->_amazon->getInfo($this->_bucket."/zftest-no-such-object/in/there"));
    }

    public function testOverwriteObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $info = $this->_amazon->getInfo($this->_bucket."/zftest");
        $this->assertEquals('"'.md5($data).'"', $info["etag"]);
        $this->assertEquals(strlen($data), $info["size"]);

        $data = "testdata with some other data";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $info = $this->_amazon->getInfo($this->_bucket."/zftest");
        $this->assertEquals('"'.md5($data).'"', $info["etag"]);
        $this->assertEquals(strlen($data), $info["size"]);
    }

    public function testRemoveObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $this->_amazon->removeObject($this->_bucket."/zftest", $data);
        $this->assertFalse($this->_amazon->getObject($this->_bucket."/zftest"));
        $this->assertFalse($this->_amazon->getInfo($this->_bucket."/zftest"));
    }

    public function testRemoveBucket()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $this->_amazon->cleanBucket($this->_bucket);
        $this->_amazon->removeBucket($this->_bucket);

        // otherwise amazon sends cached data
        sleep(3);
        $this->assertFalse($this->_amazon->isBucketAvailable($this->_bucket));
        $this->assertFalse($this->_amazon->isObjectAvailable($this->_bucket."/zftest"));
        $this->assertFalse($this->_amazon->getObjectsByBucket($this->_bucket));
        $list = $this->_amazon->getBuckets();
        $this->assertNotContains($this->_bucket, $list);
    }

    protected function _fileTest($filename, $object, $type, $exp_type, $stream = false)
    {
        if($stream) {
            $this->_amazon->putFile($filename, $object, array(S3\S3::S3_CONTENT_TYPE_HEADER => $type));
        } else {
            $this->_amazon->putFileStream($filename, $object, array(S3\S3::S3_CONTENT_TYPE_HEADER => $type));
        }

        $data = file_get_contents($filename);

        $this->assertTrue($this->_amazon->isObjectAvailable($object));

        $info = $this->_amazon->getInfo($object);
        $this->assertEquals('"'.md5_file($filename).'"', $info["etag"]);
        $this->assertEquals(filesize($filename), $info["size"]);
        $this->assertEquals($exp_type, $info["type"]);

        $fdata = $this->_amazon->getObject($object);
        $this->assertEquals($data, $fdata);
    }

    public function testPutFile()
    {
        $filedir = __DIR__."/_files/";
        $this->_amazon->createBucket($this->_bucket);

        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile", null, 'binary/octet-stream');
        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile2", 'text/plain', 'text/plain');
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3", null, 'text/html');
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3.html", 'text/plain', 'text/plain');
    }

    public function testPutFileStream()
    {
        $filedir = __DIR__."/_files/";
        $this->_amazon->createBucket($this->_bucket);

        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile", null, 'binary/octet-stream', true);
        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile2", 'text/plain', 'text/plain', true);
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3", null, 'text/html', true);
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3.html", 'text/plain', 'text/plain', true);
    }

    /**
     * Since exception post-condition is tested as well,
     * two tests are created for a given exception
     * @see testPutNoFile
     */
    public function testPutNoFileException()
    {
        $filedir = __DIR__."/_files/";

        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\RuntimeException',
            'Cannot read file ' . $filedir."nosuchfile");

        $this->_amazon->putFile($filedir."nosuchfile", $this->_bucket."/zftestfile");
    }

    /**
     * Since exception post-condition is tested as well,
     * two tests are created for a given exception
     * @see testPutNoFileException
     */
    public function testPutNoFile()
    {
        $filedir = __DIR__."/_files/";
        try {
            $this->_amazon->putFile($filedir."nosuchfile", $this->_bucket."/zftestfile");
        } catch (\Zend\Service\Amazon\Sqs\Exception\RuntimeException $e) {
            $this->assertFalse($this->_amazon->isObjectAvailable($this->_bucket."/zftestfile"));
            return;
        }
        $this->fail('Expected exception not thrown');
    }


    /**
     * @depends testCreateBucket
     * @depends testCreateObject
     */
    public function testCopyObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $info1 = $this->_amazon->getInfo($this->_bucket."/zftest");

        $this->_amazon->copyObject($this->_bucket."/zftest", $this->_bucket."/zftest2");
        $this->assertTrue($this->_amazon->isObjectAvailable($this->_bucket."/zftest"));
        $this->assertTrue($this->_amazon->isObjectAvailable($this->_bucket."/zftest2"));
        $info2 = $this->_amazon->getInfo($this->_bucket."/zftest2");

        $this->assertEquals($info1['etag'], $info2['etag']);
    }

    /**
     * @depends testCopyObject
     * @depends testRemoveObject
     */
    public function testMoveObject()
    {
        $this->_amazon->createBucket($this->_bucket);
        $data = "testdata";

        $this->_amazon->putObject($this->_bucket."/zftest", $data);
        $info1 = $this->_amazon->getInfo($this->_bucket."/zftest");

        $this->_amazon->moveObject($this->_bucket."/zftest", $this->_bucket."/zftest2");
        $this->assertFalse($this->_amazon->isObjectAvailable($this->_bucket."/zftest"));
        $this->assertTrue($this->_amazon->isObjectAvailable($this->_bucket."/zftest2"));
        $info2 = $this->_amazon->getInfo($this->_bucket."/zftest2");

        $this->assertEquals($info1['etag'], $info2['etag']);
    }

    public function testObjectEncoding()
    {
        $this->_amazon->createBucket($this->_bucket);

        $this->_amazon->putObject($this->_bucket."/this is a 100% test", "testdata");
        $this->assertEquals("testdata", $this->_amazon->getObject($this->_bucket."/this is a 100% test"));

        $this->_amazon->putObject($this->_bucket."/это тоже тест!", "testdata123");
        $this->assertEquals("testdata123", $this->_amazon->getObject($this->_bucket."/это тоже тест!"));
    }


    public function testCreateBucketWithBadName()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Bucket name "This is a Very Bad Name" contains invalid characters');
        $this->_amazon->createBucket("This is a Very Bad Name");
    }

    public function testBucketAvailabilityWithBadName()
    {
        $this->setExpectedException(
            '\Zend\Http\Client\Adapter\Exception',
            'Unable to Connect to tcp://This is a Very Bad Name.s3.amazonaws.com:80');
        $this->_amazon->isBucketAvailable("This is a Very Bad Name");
    }

    public function testPutObjectWithBadName()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Bucket name "This is a Very Bad Name" contains invalid characters');
        $this->_amazon->putObject("This is a Very Bad Name/And It Gets Worse", "testdata");
    }

    public function testGetObjectWithBadName()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Bucket name "This is a Very Bad Name" contains invalid characters');
        $this->_amazon->getObject("This is a Very Bad Name/And It Gets Worse");
    }

    public function testGetInfoWithBadName()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Bucket name "This is a Very Bad Name" contains invalid characters');
        $this->_amazon->getInfo("This is a Very Bad Name/And It Gets Worse");
    }

    public function testSetEndpointWithBadName()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Invalid endpoint supplied');
        $this->_amazon->setEndpoint("This is a Very Bad Name/And It Gets Worse");
    }

    public function testBucketNameIsTooShort()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            sprintf('Bucket name "%s" must be between 3 and 255 characters long', 'xx'));
        $this->_amazon->createBucket('xx');
    }

    public function testBucketNameIsTooLong()
    {
        $bucketName = str_repeat('x', 256);
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            sprintf('Bucket name "%s" must be between 3 and 255 characters long', $bucketName));
        $this->_amazon->createBucket($bucketName);
    }

    public function testAcl()
    {
        $this->_amazon->createBucket($this->_bucket);
        $filedir = __DIR__."/_files/";

        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/zftestfile.html");
        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/zftestfile2.html",
            array(S3\S3::S3_ACL_HEADER => S3\S3::S3_ACL_PUBLIC_READ));

        $url = 'http://' . S3\S3::S3_ENDPOINT."/".$this->_bucket."/zftestfile.html";
        $data = @file_get_contents($url);
        $this->assertFalse($data);

        $url = 'http://' . S3\S3::S3_ENDPOINT."/".$this->_bucket."/zftestfile2.html";
        $data = @file_get_contents($url);
        $this->assertEquals(file_get_contents($filedir."testdata.html"), $data);
    }

    /**
     * Test bucket name with /'s and encoding
     *
     * ZF-6855
     *
     */
    public function testObjectPath()
    {
        $this->_amazon->createBucket($this->_bucket);
        $filedir = __DIR__."/_files/";
        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/subdir/dir with spaces/zftestfile.html",
            array(S3\S3::S3_ACL_HEADER => S3\S3::S3_ACL_PUBLIC_READ));
        $url = 'http://' . S3\S3::S3_ENDPOINT."/".$this->_bucket."/subdir/dir%20with%20spaces/zftestfile.html";
        $data = @file_get_contents($url);
        $this->assertEquals(file_get_contents($filedir."testdata.html"), $data);
    }

    /**
     * Test creating object with https
     *
     * ZF-7029
     */
    public function testCreateObjectSSL()
    {
        $endpoint = $this->_amazon->getEndpoint();
        $this->_amazon->setEndpoint('https://s3.amazonaws.com');
        $this->assertEquals('https://s3.amazonaws.com', $this->_amazon->getEndpoint()->generate());
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket."/zftest", "testdata");
        $this->assertEquals("testdata", $this->_amazon->getObject($this->_bucket."/zftest"));
        $this->_amazon->setEndpoint($endpoint);
    }

    /**
     * Test creating bucket with IP
     *
     * ZF-6686
     */
    public function testBucketIPMaskException()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException',
            'Bucket name "127.0.0.1" cannot be an IP address');
        $this->_amazon->createBucket("127.0.0.1");
    }

    /**
     * Test creating bucket with IP
     *
     * ZF-6686
     */
    public function testBucketIPMaskPostCondition()
    {
        try {
            $this->_amazon->createBucket("127.0.0.1");
        } catch(\Zend\Service\Amazon\Sqs\Exception\InvalidArgumentException $e) {
            $this->_amazon->createBucket("123-456-789-123");
            $this->assertTrue($this->_amazon->isBucketAvailable("123-456-789-123"));
            $this->_amazon->removeBucket("123-456-789-123");
            return;
        }
        $this->fail("Failed to throw expected exception");

    }

    /**
     *  @group ZF-7773
     */
    public function testGetObjectsByBucketParams()
    {
        $this->_amazon->createBucket("testgetobjectparams1");
        $this->_amazon->putObject("testgetobjectparams1/zftest1", "testdata");
        $this->_amazon->putObject("testgetobjectparams1/zftest2", "testdata");

        $list = $this->_amazon->getObjectsByBucket("testgetobjectparams1", array('max-keys' => 1));
        $this->assertEquals(1, count($list));

        $this->_amazon->removeObject("testgetobjectparams1/zftest1", "testdata");
        $this->_amazon->removeObject("testgetobjectparams1/zftest2", "testdata");
        $this->_amazon->removeBucket("testgetobjectparams1");
    }
    
    public function testCommonPrefixes()
    {
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket.'/test-folder/test1','test');
        $this->_amazon->putObject($this->_bucket.'/test-folder/test2-folder/','');
        $params= array(
                    'prefix' => 'test-folder/',
                    'delimiter' => '/'
                 );
        $response= $this->_amazon->getObjectsAndPrefixesByBucket($this->_bucket,$params);
        $this->assertEquals($response['objects'][0],'test-folder/test1');
        $this->assertEquals($response['prefixes'][0],'test-folder/test2-folder/');
    }

    public function tearDown()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            return;
        }
        unset($this->_amazon->debug);
        $this->_amazon->cleanBucket($this->_bucket);
        $this->_amazon->removeBucket($this->_bucket);
    }
}
