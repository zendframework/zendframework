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
 * @package    Zend\Cloud\StorageService\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\StorageService\Adapter;

use ZendTest\Cloud\StorageService\TestCase,
    Zend\Service\Rackspace\Files as RackspaceService,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend\Cloud\StorageService\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
