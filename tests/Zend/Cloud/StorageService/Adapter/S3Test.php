<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\StorageService\Adapter;

use ZendTest\Cloud\StorageService\TestCase;
use Zend\Service\Amazon\S3\S3 as AmazonS3;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 */
class S3Test extends TestCase
{
    protected $_clientType = 'Zend\Service\Amazon\S3\S3';

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Create the bucket here
        $s3 = new AmazonS3(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::AWS_ACCESS_KEY),
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::AWS_SECRET_KEY)
        );

        $s3->createBucket(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::BUCKET_NAME)
        );
    }

    // TODO: Create a custom test for S3 that checks fetchMetadata() with an object that has custom metadata.
    public function testFetchMetadata()
    {
        $this->markTestIncomplete('S3 doesn\'t support storing metadata after an item is created.');
    }

    public function testStoreMetadata()
    {
        $this->markTestSkipped('S3 doesn\'t support storing metadata after an item is created.');
    }

    public function testDeleteMetadata()
    {
        $this->markTestSkipped('S3 doesn\'t support storing metadata after an item is created.');
    }


    /**
     * Tears down this test case
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->_config) {
            return;
        }

        // Delete the bucket here
        $s3 = new AmazonS3(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::AWS_ACCESS_KEY),
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::AWS_SECRET_KEY)
        );
        $s3->removeBucket(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\S3::BUCKET_NAME)
        );
        parent::tearDown();
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_S3_BUCKET')
        ) {
            $this->markTestSkipped("Amazon S3 access not configured, skipping test");
        }

        $config = new \Zend\Config\Config(array(
            \Zend\Cloud\StorageService\Factory::STORAGE_ADAPTER_KEY => 'Zend\Cloud\StorageService\Adapter\S3',
            \Zend\Cloud\StorageService\Adapter\S3::AWS_ACCESS_KEY   => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            \Zend\Cloud\StorageService\Adapter\S3::AWS_SECRET_KEY   => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'),
            \Zend\Cloud\StorageService\Adapter\S3::BUCKET_NAME      => constant('TESTS_ZEND_SERVICE_AMAZON_S3_BUCKET'),
        ));

        return $config;
    }
}
