<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\QueueService\Adapter;

use ZendTest\Cloud\QueueService\TestCase;
use Zend\Cloud\QueueService\Adapter\Sqs;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_QueueService_Adapter
 * @subpackage UnitTests
 */
class SqsTest extends TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;

    protected $_clientType = 'Zend\Service\Amazon\Sqs';

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // Isolate the tests from slow deletes
        $this->_wait();
    }

    public function testListQueues()
    {
        try {
            $queues = $this->_commonQueue->listQueues();
            $this->_wait();
            if (count($queues)) {
                foreach ($queues as $queue) {
                    $this->_commonQueue->deleteQueue($queue);
                    $this->_wait();
                }
            }

            $queueURL1 = $this->_commonQueue->createQueue('test-list-queue1');
            $this->assertNotNull($queueURL1);
            $this->_wait();

            $queueURL2 = $this->_commonQueue->createQueue('test-list-queue2');
            $this->assertNotNull($queueURL2);

            // Wait 30s after creation to ensure we can retrieve it
            $this->_wait(30);

            $queues = $this->_commonQueue->listQueues();
            $errorMessage = "Final queues are ";
            foreach ($queues as $queue) {
                $errorMessage .= $queue . ', ';
            }
            $errorMessage .= "\nHave queue URLs $queueURL1 and $queueURL2\n";
            $this->assertEquals(2, count($queues), $errorMessage);

            // PHPUnit does an identical comparison for assertContains(), so we just
            // use assertTrue and in_array()
            $this->assertTrue(in_array($queueURL1, $queues));
            $this->assertTrue(in_array($queueURL2, $queues));

            $this->_commonQueue->deleteQueue($queueURL1);
            $this->_commonQueue->deleteQueue($queueURL2);
        } catch (Exception $e) {
            if (isset($queueURL1)) {
                $this->_commonQueue->deleteQueue($queueURL1);
            }
            if (isset($queueURL2)) {
                $this->_commonQueue->deleteQueue($queueURL2);
            }
            throw $e;
        }
    }

    public function testStoreQueueMetadata()
    {
        $this->markTestSkipped('SQS does not currently support storing metadata');
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        ) {
            $this->markTestSkipped("Amazon SQS access not configured, skipping test");
        }

        $config = new Config(array(
            Factory::QUEUE_ADAPTER_KEY => 'Zend\Cloud\QueueService\Adapter\Sqs',
            Sqs::AWS_ACCESS_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            Sqs::AWS_SECRET_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'),
            ));

        return $config;
    }
}
