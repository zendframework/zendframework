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

use Zend\Cloud\QueueService\Adapter,
    Zend\Config\Config,
    Zend\Cloud\QueueService\Factory,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * This class forces the adapter tests to implement tests for all methods on
 * Zend\Cloud_QueueService.
 *
 * @category   Zend
 * @package    Zend_Cloud_QueueService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Reference to queue adapter to test
     *
     * @var \Zend\Cloud\QueueService
     */
    protected $_commonQueue;
    protected $_dummyNamePrefix = '/TestItem';
    protected $_dummyDataPrefix = 'TestData';
    protected $_clientType = 'stdClass';
    
    /**
     * Config object
     *
     * @var \Zend\Config\Config
     */
    protected $_config;

    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 1;

    public function setUp()
    {
        $this->_config = $this->_getConfig();
        $this->_commonQueue = Factory::getAdapter($this->_config);
    }

    public function testGetClient()
    {
        $this->assertInstanceOf($this->_clientType, $this->_commonQueue->getClient());
    }

    public function testCreateQueue()
    {
        try {
            // Create timeout default should be 30 seconds
            $startTime = time();
            $queueURL = $this->_commonQueue->createQueue('test-create-queue');
            $endTime = time();
            $this->assertNotNull($queueURL);
            $this->assertLessThan(30, $endTime - $startTime);
            $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if(isset($queueURL)) $this->_commonQueue->deleteQueue($queueURL);
            throw $e;
        }
    }

    public function testDeleteQueue()
    {
        try {
            $queueURL = $this->_commonQueue->createQueue('test-delete-queue');
            $this->assertNotNull($queueURL);

            $this->_commonQueue->deleteQueue($queueURL);

            $this->_wait();
            $this->_wait();
            $this->_wait();
            try {
                $messages = $this->_commonQueue->receiveMessages($queueURL);
                $this->fail('An exception should have been thrown if the queue has been deleted; received ' . var_export($messages, 1));
            } catch (\Zend\Cloud\QueueService\Exception\ExceptionInterface $e) {
                $this->assertTrue(true);
                $this->_commonQueue->deleteQueue($queueURL);
                return;
            }
        } catch (Exception $e) {
            if(isset($queueURL)) $this->_commonQueue->deleteQueue($queueURL);
            throw $e;
        }
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
            $this->_wait();

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

    public function testStoresAndFetchesQueueMetadata()
    {
        try {
            $queueURL = $this->_commonQueue->createQueue('test-fetch-queue-metadata');
            $this->assertNotNull($queueURL);
            $this->_wait();
            $this->_commonQueue->storeQueueMetadata($queueURL, array('purpose' => 'test'));
            $this->_wait();
            $metadata = $this->_commonQueue->fetchQueueMetadata($queueURL);
            $this->assertTrue(is_array($metadata));
            $this->assertGreaterThan(0, count($metadata));
            $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if (isset($queueURL)) {
                $this->_commonQueue->deleteQueue($queueURL);
            }
            throw $e;
        }
    }

    public function testSendMessage()
    {
        try {
            $queueURL = $this->_commonQueue->createQueue('test-send-message');
            $this->assertNotNull($queueURL);
            $this->_wait();
            $message = 'testSendMessage - Message 1';
            $this->_commonQueue->sendMessage($queueURL, $message);
            $this->_wait();
            $receivedMessages = $this->_commonQueue->receiveMessages($queueURL);
            $this->assertInstanceOf('Zend\Cloud\QueueService\MessageSet', $receivedMessages);
            $this->assertEquals(1, count($receivedMessages));
            foreach ($receivedMessages as $m) {
                $this->assertEquals($message, $m->getBody());
            }
		  $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if(isset($queueURL)) $this->_commonQueue->deleteQueue($queueURL);
            throw $e;
        }
    }

    public function testReceiveMessages()
    {
        $queueURL = null;
        try {
            $queueURL = $this->_commonQueue->createQueue('test-receive-messages');
            $this->assertNotNull($queueURL);
            $this->_wait();

            $message1 = 'testReceiveMessages - Message 1';
            $message2 = 'testReceiveMessages - Message 2';
            $this->_commonQueue->sendMessage($queueURL, $message1);
            $this->_commonQueue->sendMessage($queueURL, $message2);
            $this->_wait();
            $this->_wait();

            // receive one message
            $receivedMessages1 = $this->_commonQueue->receiveMessages($queueURL);
            $this->assertInstanceOf('Zend\Cloud\QueueService\MessageSet', $receivedMessages1);
            $this->assertEquals(1, count($receivedMessages1));
            foreach ($receivedMessages1 as $receivedMessage1) {
                $this->assertInstanceOf('Zend\Cloud\QueueService\Message', $receivedMessage1);
            }

            // cleanup the queue
            foreach ($receivedMessages1 as $message) {
                $this->_commonQueue->deleteMessage($queueURL, $message);
            }
            $this->_wait();
            $this->_wait();

            // send 2 messages again
            $this->_commonQueue->sendMessage($queueURL, $message1);
            $this->_commonQueue->sendMessage($queueURL, $message2);
            $this->_wait();
            $this->_wait();
            $this->_wait();
            $this->_wait();

            // receive both messages
            $receivedMessages2 = $this->_commonQueue->receiveMessages($queueURL, 2);
            $this->assertInstanceOf('Zend\Cloud\QueueService\MessageSet', $receivedMessages2);
            $this->assertEquals(2, count($receivedMessages2));

            $tests = array();
            foreach ($receivedMessages2 as $message) {
                $tests[] = $message;
            }
            $texts = array($tests[0]->getBody(), $tests[1]->getBody());
            $this->assertContains($message1, $texts);
            $this->assertContains($message2, $texts);

            $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if (isset($queueURL)) {
                $this->_commonQueue->deleteQueue($queueURL);
            }
            throw $e;
        }
    }

    public function testDeleteMessage()
    {
        try {
            $queueURL = $this->_commonQueue->createQueue('test-delete-messages');
            $this->assertNotNull($queueURL);
            $this->_wait();
            $this->_wait();
            $message1 = 'testDeleteMessage - Message 1';
            $this->_commonQueue->sendMessage($queueURL, $message1);
            $this->_wait();
            $this->_wait();
            $receivedMessages1 = $this->_commonQueue->receiveMessages($queueURL);

            // should receive one $message1
            $this->assertInstanceOf('Zend\Cloud\QueueService\MessageSet', $receivedMessages1);
            $this->assertEquals(1, count($receivedMessages1));
            foreach ($receivedMessages1 as $receivedMessage1) {
                $this->assertEquals($message1, $receivedMessage1->getBody());
            }
            $this->_commonQueue->deleteMessage($queueURL, $receivedMessage1);
            $this->_wait();
            $this->_wait();

            // now there should be no messages left
            $receivedMessages2 = $this->_commonQueue->receiveMessages($queueURL);
            $this->assertInstanceOf('Zend\Cloud\QueueService\MessageSet', $receivedMessages2);
		    $this->assertEquals(0, count($receivedMessages2));

		    $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if(isset($queueURL)) $this->_commonQueue->deleteQueue($queueURL);
            throw $e;
        }
    }

    public function testPeekMessages()
    {
        try {
            $queueURL = $this->_commonQueue->createQueue('test-peek-messages');
            $this->assertNotNull($queueURL);
            $this->_wait();
            $message1 = 'testPeekMessage - Message 1';
            $this->_commonQueue->sendMessage($queueURL, $message1);
            $this->_wait();
            $peekedMessages = $this->_commonQueue->peekMessages($queueURL, 1);
            foreach ($peekedMessages as $message) {
                $this->assertEquals($message1, $message->getBody());
                break;
            }
            // and again
            $peekedMessages = $this->_commonQueue->peekMessages($queueURL, 1);
            foreach ($peekedMessages as $message) {
                $this->assertEquals($message1, $message->getBody());
                break;
            }

            $this->_commonQueue->deleteQueue($queueURL);
        } catch (Exception $e) {
            if(isset($queueURL)) $this->_commonQueue->deleteQueue($queueURL);
            throw $e;
        }
    }

    protected function _wait($duration = null)
    {
        if (null === $duration) {
            $duration = $this->_waitPeriod;
        }
        sleep($duration);
    }

    /**
     * Get adapter configuration for concrete test
     *
     * @returns \Zend\Config\Config
     */
    abstract protected function _getConfig();
}
