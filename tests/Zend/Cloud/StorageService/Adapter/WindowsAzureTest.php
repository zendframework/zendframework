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
    Zend\Cloud\StorageService\Adapter\WindowsAzure,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WindowsAzureTest extends TestCase
{
	protected $_clientType = 'Zend\Service\WindowsAzure\Storage\Blob';

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME')
            || !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY')
            || !defined('TESTS_ZEND_CLOUD_STORAGE_WINDOWSAZURE_CONTAINER')
        ) {
            $this->markTestSkipped("Windows Azure access not configured, skipping test");
        }

        $config = new \Zend\Config\Config(array(
            \Zend\Cloud\StorageService\Factory::STORAGE_ADAPTER_KEY => 'Zend\Cloud\StorageService\Adapter\WindowsAzure',
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::ACCOUNT_NAME => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::ACCOUNT_KEY => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_HOST'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::PROXY_HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_HOST'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::PROXY_PORT => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_PORT'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::PROXY_CREDENTIALS => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_CREDENTIALS'),
            \Zend\Cloud\StorageService\Adapter\WindowsAzure::CONTAINER => constant('TESTS_ZEND_CLOUD_STORAGE_WINDOWSAZURE_CONTAINER'),
        ));

        return $config;
    }
}
