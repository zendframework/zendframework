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
 * @package    Zend_Cloud_QueueService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\QueueService;

use Zend\Config\Factory as ConfigFactory,
    Zend\Cloud\QueueService\Factory,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * Test class for \Zend\Cloud\QueueService\Factory
 *
 * @category   Zend
 * @package    Zend_Cloud_QueueService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cloud
 */
class FactoryTest extends PHPUnitTestCase
{
    public function testGetQueueAdapterKey()
    {
        $this->assertTrue(is_string(Factory::QUEUE_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig()
    {
        // SQS adapter
        $sqsConfig = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/sqs.ini'), true);
        $sqsAdapter = Factory::getAdapter($sqsConfig);
        $this->assertEquals('Zend\Cloud\QueueService\Adapter\Sqs', get_class($sqsAdapter));

        // Zend queue adapter
        $zqConfig = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/zendqueue.ini'), true);
        $zq = Factory::getAdapter($zqConfig);
        $this->assertEquals('Zend\Cloud\QueueService\Adapter\ZendQueue', get_class($zq));

        // Azure adapter
        //$azureConfig = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/windowsazure.ini'), true);
        //$azureAdapter = Factory::getAdapter($azureConfig);
        //$this->assertEquals('Zend\Cloud\QueueService\Adapter\WindowsAzure', get_class($azureAdapter));
    }

    public function testGetAdapterWithArray()
    {
        // No need to overdo it; we'll test the array config with just one adapter.
        $zqConfig = array(Factory::QUEUE_ADAPTER_KEY => 'Zend\Cloud\QueueService\Adapter\ZendQueue',
                          \Zend\Cloud\QueueService\Adapter\ZendQueue::ADAPTER => "ArrayAdapter");

        $zq = Factory::getAdapter($zqConfig);

        $this->assertEquals('Zend\Cloud\QueueService\Adapter\ZendQueue', get_class($zq));
    }
}
