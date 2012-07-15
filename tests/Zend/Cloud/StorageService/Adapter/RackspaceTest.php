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
use Zend\Service\Rackspace\Files as RackspaceService;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 */
class RackspaceTest extends TestCase
{
    protected $_clientType = 'Zend\Service\Rackspace\Files';

    public function testFetchItemStream()
    {
        // The Rackspace client library doesn't support streams
        $this->markTestSkipped('The Rackspace client library doesn\'t support streams.');
    }

    public function testStoreItemStream()
    {
        // The Rackspace client library doesn't support streams
        $this->markTestSkipped('The Rackspace client library doesn\'t support streams.');
    }

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED')) {
            $this->markTestSkipped('Rackspace online tests are not enabled');
        }

        parent::setUp();
        $this->_waitPeriod = 5;

        // Create the container here
        $rackspace= new RackspaceService(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::USER),
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::API_KEY)
        );
        $rackspace->createContainer(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::REMOTE_CONTAINER)
        );

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

        // Delete the container here
        $rackspace = new RackspaceService(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::USER),
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::API_KEY)
        );
        $files = $rackspace->getObjects(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::REMOTE_CONTAINER)
        );
        if ($files==!false) {
            foreach ($files as $file) {
                $rackspace->deleteObject(
                    $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::REMOTE_CONTAINER),
                    $file->getName()
                );
            }
        }
        $rackspace->deleteContainer(
            $this->_config->get(\Zend\Cloud\StorageService\Adapter\Rackspace::REMOTE_CONTAINER)
        );

        parent::tearDown();
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER')
            || !defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY')
            || !defined('TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME')
        ) {
            $this->markTestSkipped("Rackspace access not configured, skipping test");
        }

        $config = new \Zend\Config\Config(array(
            \Zend\Cloud\StorageService\Factory::STORAGE_ADAPTER_KEY       => 'Zend\Cloud\StorageService\Adapter\Rackspace',
            \Zend\Cloud\StorageService\Adapter\Rackspace::USER            => constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER'),
            \Zend\Cloud\StorageService\Adapter\Rackspace::API_KEY          => constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY'),
            \Zend\Cloud\StorageService\Adapter\Rackspace::REMOTE_CONTAINER => constant('TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME')
        ));

        return $config;
    }
}
