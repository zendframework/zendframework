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
    Zend\Cloud\StorageService\Adapter\Nirvanix,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
