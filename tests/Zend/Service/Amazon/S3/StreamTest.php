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
 * @version    $Id: OnlineTest.php 8064 2008-02-16 10:58:39Z thomas $
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
 */
class Zend_Service_Amazon_S3_StreamTest extends PHPUnit_Framework_TestCase
{
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
        $this->_bucketName = "s3://".constant('TESTS_ZEND_SERVICE_AMAZON_S3_BUCKET');
        $this->_fileName = $this->_bucketName."/sample_file.txt";

        $this->_amazon->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);
        $this->_amazon->registerStreamWrapper();
        $this->_amazon->cleanBucket($this->_bucket);
        $this->_amazon->removeBucket($this->_bucket);
        // terms of use compliance: no more than one query per second
        sleep(1);
    }

    /**
     * Tear down each test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_amazon->unregisterStreamWrapper();
	$buckets = $this->_amazon->getBuckets();
	foreach($buckets as $bucket) {
	        $this->_amazon->cleanBucket($bucket);
		$this->_amazon->removeBucket($bucket);
	}
    }

    /**
     * Test creating and removing buckets
     *
     * @return void
     */
    public function testBuckets()
    {
        // Create the bucket
        $result = mkdir($this->_bucketName);
        $this->assertTrue($result);
        // Remove the bucket
        $result = rmdir($this->_bucketName);
        $this->assertTrue($result);
    }

    /**
     * Test writing to an object
     *
     * @return void
     */
    public function testWriteObject()
    {
        // Create the bucket
        $result = mkdir($this->_bucketName);
        $this->assertTrue($result);

        // Generate sample data
        $data = str_repeat('x', 10000);

        // Write to an object
        $size = file_put_contents($this->_fileName, $data);
        $this->assertEquals(strlen($data), $size);

        // Write to an object
        $f = fopen($this->_fileName, 'w');
        for ($i = 0; $i < 100; $i++) {
            fwrite($f, 'x');
        }
        fclose($f);

        unset($data);

        // Check object size
        $size = filesize($this->_fileName);
        $this->assertEquals(100, $size);

        // Remove the object
        $result = unlink($this->_fileName);
        $this->assertTrue($result);
    }

    /**
     * Test reading from an object
     *
     * @return void
     */
    public function testReadObject()
    {
        // Create the bucket
        $result = mkdir($this->_bucketName);
        $this->assertTrue($result);

        // Generate sample data
        $data = str_repeat('x', 10000);

        // Write to an object
        $size = file_put_contents($this->_fileName, $data);
        $this->assertEquals(strlen($data), $size);

        // Read from an object
        $new_data = file_get_contents($this->_fileName);

        $this->assertEquals($data, $new_data);

        // Read from an oject
        $new_data = '';

        $f = fopen($this->_fileName, 'r');
        while (!feof($f)) {
            $new_data .= fread($f, 1024);
        }
        fclose($f);

        $this->assertEquals($data, $new_data);

        unset($data);
        unset($new_data);

        // Remove the object
        $result = unlink($this->_fileName);
        $this->assertTrue($result);
    }

    /**
     * Test getting the list of available buckets
     *
     * @return void
     */
    public function testGetBucketList()
    {
        $buckets = array('zf-test1', 'zf-test2', 'zf-test3');

        // Create the buckets
        foreach ($buckets as $bucket) {
            $result = mkdir('s3://'.$bucket);
            $this->assertTrue($result);
        }

        $online_buckets = array();

        // Retrieve list of buckets on S3
        $e = opendir('s3://');
        while (($f = readdir($e)) !== false) {
            $online_buckets[] = $f;
        }
        closedir($e);

        // Check that each bucket is in our original list
        foreach ($online_buckets as $bucket) {
            $this->assertContains($bucket, $buckets);
        }

        // Remove the buckets
        foreach ($buckets as $bucket) {
            $result = rmdir('s3://'.$bucket);
            $this->assertTrue($result);
        }
    }

    /**
     * Test object stat
     *
     * @return void
     */
    public function testObjectStat()
    {
        // Create the bucket
        $result = mkdir($this->_bucketName);
        $this->assertTrue($result);

	$this->assertTrue(is_dir($this->_bucketName));

        $data = str_repeat('x', 10000);
        $len = strlen($data);

        // Write to an object
        $size = file_put_contents($this->_fileName, $data);
        $this->assertEquals($len, $size);

	$this->assertFalse(is_dir($this->_fileName));

        // Stat an object
        $info = stat($this->_fileName);
        $this->assertEquals($len, $info['size']);

        unset($data);

        // Remove the object
        $result = unlink($this->_fileName);
        $this->assertTrue($result);
    }
}


class Zend_Service_Amazon_S3_StreamTest_Skip extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Service_Amazon_S3 online tests not enabled with an access key ID and '
                             . ' secret key ID in TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
