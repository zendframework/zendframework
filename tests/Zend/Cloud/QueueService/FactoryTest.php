<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\QueueService;

use Zend\Config\Factory as ConfigFactory;
use Zend\Cloud\QueueService\Factory;
use PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * Test class for \Zend\Cloud\QueueService\Factory
 *
 * @category   Zend
 * @package    Zend_Cloud_QueueService
 * @subpackage UnitTests
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
