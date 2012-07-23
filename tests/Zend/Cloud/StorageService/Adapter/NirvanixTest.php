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
use Zend\Cloud\StorageService\Adapter\Nirvanix;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 */
class NirvanixTest extends TestCase
{
    protected $_clientType = 'Zend\Service\Nirvanix';

    public function testFetchItemStream()
    {
        // The Nirvanix client library doesn't support streams
        $this->markTestSkipped('The Nirvanix client library doesn\'t support streams.');
    }

    public function testStoreItemStream()
    {
        // The Nirvanix client library doesn't support streams
        $this->markTestSkipped('The Nirvanix client library doesn\'t support streams.');
    }

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_waitPeriod = 5;
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_USERNAME')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ACCESSKEY')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_PASSWORD')
            || !defined('TESTS_ZEND_CLOUD_STORAGE_NIRVANIX_DIRECTORY')
        ) {
            $this->markTestSkipped("Nirvanix access not configured, skipping test");
        }

        $config = new \Zend\Config\Config(array(
            \Zend\Cloud\StorageService\Factory::STORAGE_ADAPTER_KEY       => 'Zend\Cloud\StorageService\Adapter\Nirvanix',
            \Zend\Cloud\StorageService\Adapter\Nirvanix::USERNAME         => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_USERNAME'),
            \Zend\Cloud\StorageService\Adapter\Nirvanix::APP_KEY          => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ACCESSKEY'),
            \Zend\Cloud\StorageService\Adapter\Nirvanix::PASSWORD         => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_PASSWORD'),
            \Zend\Cloud\StorageService\Adapter\Nirvanix::REMOTE_DIRECTORY => constant('TESTS_ZEND_CLOUD_STORAGE_NIRVANIX_DIRECTORY'),
        ));

        return $config;
    }
}
