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
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\StorageService\Adapter;

use ZendTest\Cloud\StorageService\TestCase,
    Zend\Service\Amazon\S3\S3 as AmazonS3,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
