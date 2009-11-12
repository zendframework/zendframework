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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Service_Amazon
 */
require_once 'Zend/Service/Amazon/S3.php';

/**
 * @see Zend_Http_Client_Adapter_Socket
 */
require_once 'Zend/Http/Client/Adapter/Socket.php';


/**
 * @category   Zend
 * @package    Zend_Service_Amazon_S3
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_S3
 */
class Zend_Service_Amazon_S3_OnlineTest extends PHPUnit_Framework_TestCase
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
        $this->_amazon = new Zend_Service_Amazon_S3(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
                                                    constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
                                                    );
        $this->_nosuchbucket = "nonexistingbucketnamewhichnobodyshoulduse";
        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

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

        $this->assertFalse($this->_amazon->isBucketAvailable($this->_bucket));
        $this->assertFalse($this->_amazon->isObjectAvailable($this->_bucket."/zftest"));
        $this->assertFalse($this->_amazon->getObjectsByBucket($this->_bucket));
        $list = $this->_amazon->getBuckets();
        $this->assertNotContains($this->_bucket, $list);
    }

    protected function _fileTest($filename, $object, $type, $exp_type)
    {
        $this->_amazon->putFile($filename, $object, array(Zend_Service_Amazon_S3::S3_CONTENT_TYPE_HEADER => $type));

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
        $filedir = dirname(__FILE__)."/_files/";
        $this->_amazon->createBucket($this->_bucket);

        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile", null, 'binary/octet-stream');
        $this->_fileTest($filedir."testdata", $this->_bucket."/zftestfile2", 'text/plain', 'text/plain');
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3", null, 'text/html');
        $this->_fileTest($filedir."testdata.html", $this->_bucket."/zftestfile3.html", 'text/plain', 'text/plain');
    }

    public function testPutNoFile()
    {
        $filedir = dirname(__FILE__)."/_files/";

        try {
            $this->_amazon->putFile($filedir."nosuchfile", $this->_bucket."/zftestfile");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("Cannot read", $e->getMessage());
            $this->assertContains("nosuchfile", $e->getMessage());
        }
        $this->assertFalse($this->_amazon->isObjectAvailable($this->_bucket."/zftestfile"));
    }

    public function testObjectEncoding()
    {
        $this->_amazon->createBucket($this->_bucket);

        $this->_amazon->putObject($this->_bucket."/this is a 100% test", "testdata");
        $this->assertEquals("testdata", $this->_amazon->getObject($this->_bucket."/this is a 100% test"));

        $this->_amazon->putObject($this->_bucket."/это тоже тест!", "testdata123");
        $this->assertEquals("testdata123", $this->_amazon->getObject($this->_bucket."/это тоже тест!"));
    }

    public function testBadNames()
    {
        try {
            $this->_amazon->createBucket("This is a Very Bad Name");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("contains invalid characters", $e->getMessage());
        }
        try {
            $this->_amazon->isBucketAvailable("This is a Very Bad Name");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Uri_Exception $e) {
            $this->assertContains("not a valid HTTP host", $e->getMessage());
        }
        try {
            $this->_amazon->putObject("This is a Very Bad Name/And It Gets Worse", "testdata");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("contains invalid characters", $e->getMessage());
        }
        try {
            $this->_amazon->getObject("This is a Very Bad Name/And It Gets Worse");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("contains invalid characters", $e->getMessage());
        }
        try {
            $this->_amazon->getInfo("This is a Very Bad Name/And It Gets Worse");
            $this->fail("Expected exception not thrown");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("contains invalid characters", $e->getMessage());
        }
    }

    public function testAcl()
    {
        $this->_amazon->createBucket($this->_bucket);
        $filedir = dirname(__FILE__)."/_files/";

        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/zftestfile.html");
        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/zftestfile2.html",
            array(Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ));

        $url = 'http://' . Zend_Service_Amazon_S3::S3_ENDPOINT."/".$this->_bucket."/zftestfile.html";
        $data = @file_get_contents($url);
        $this->assertFalse($data);

        $url = 'http://' . Zend_Service_Amazon_S3::S3_ENDPOINT."/".$this->_bucket."/zftestfile2.html";
        $data = @file_get_contents($url);
        $this->assertEquals(file_get_contents($filedir."testdata.html"), $data);
    }

    /**
     * Test creating bucket with location
     * ZF-6728
     *
     */
    public function testCreateBucketEU()
    {
        $this->_amazon->createBucket($this->_bucket, 'EU');
        $this->assertTrue($this->_amazon->isBucketAvailable($this->_bucket));
        $list = $this->_amazon->getBuckets();
        $this->assertContains($this->_bucket, $list);
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
        $filedir = dirname(__FILE__)."/_files/";
        $this->_amazon->putFile($filedir."testdata.html", $this->_bucket."/subdir/dir with spaces/zftestfile.html",
            array(Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ));
        $url = 'http://' . Zend_Service_Amazon_S3::S3_ENDPOINT."/".$this->_bucket."/subdir/dir%20with%20spaces/zftestfile.html";
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
        $this->_amazon->setEndpoint('https://s3.amazonaws.com');
        $this->assertEquals('https://s3.amazonaws.com', $this->_amazon->getEndpoint()->getUri());
        $this->_amazon->createBucket($this->_bucket);
        $this->_amazon->putObject($this->_bucket."/zftest", "testdata");
        $this->assertEquals("testdata", $this->_amazon->getObject($this->_bucket."/zftest"));
    }

    /**
     * Test creating bucket with IP
     *
     * ZF-6686
     */
    public function testBucketIPMask()
    {
        try {
            $this->_amazon->createBucket("127.0.0.1");
            $this->fail("Failed to throw expected exception");
        } catch(Zend_Service_Amazon_S3_Exception $e) {
            $this->assertContains("cannot be an IP address", $e->getMessage());
        }
        $this->_amazon->createBucket("123-456-789-123");
        $this->assertTrue($this->_amazon->isBucketAvailable("123-456-789-123"));
        $this->_amazon->removeBucket("123-456-789-123");
    }

    /**
     *  @see ZF-7773
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

    public function tearDown()
    {
        unset($this->_amazon->debug);
        $this->_amazon->cleanBucket($this->_bucket);
        $this->_amazon->removeBucket($this->_bucket);
    }
}


/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_S3
 */
class Zend_Service_Amazon_S3_OnlineTest_Skip extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Service_Amazon_S3 online tests not enabled with an access key ID in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
