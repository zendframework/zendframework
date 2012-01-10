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
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_WindowsAzure_Storage_Queue 
 */

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_QueueStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setup
     */
    protected function setUp()
    {
        if (!TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $this->markTestSkipped('Windows Azure Tests disabled');
        }
    }

    /**
     * Test teardown
     */
    protected function tearDown()
    {
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteQueue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_PREFIX . $i); } catch (Exception $e) { }
        }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Queue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Queue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::retryN(10, 250));
        }

        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }

    protected static $uniqId = 0;

    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_PREFIX . self::$uniqId;
    }

    /**
     * Test queue exists
     */
    public function testQueueExists()
    {
        $queueName1 = $this->generateName();
        $queueName2 = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName1);
        $storageClient->createQueue($queueName2);

        $result = $storageClient->queueExists($queueName1);
        $this->assertTrue($result);

        $result = $storageClient->queueExists(md5(time()));
        $this->assertFalse($result);
    }

    /**
     * Test create queue
     */
    public function testCreateQueue()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $result = $storageClient->createQueue($queueName);
        $this->assertEquals($queueName, $result->Name);
    }

    /**
     * Test set queue metadata
     */
    public function testSetQueueMetadata()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);

        $storageClient->setQueueMetadata($queueName, array(
            'createdby' => 'PHPAzure',
        ));

        $metadata = $storageClient->getQueueMetadata($queueName);
        $this->assertEquals('PHPAzure', $metadata['createdby']);
    }

    /**
     * Test get queue
     */
    public function testGetQueue()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);

        $queue = $storageClient->getQueue($queueName);
        $this->assertEquals($queueName, $queue->Name);
        $this->assertEquals(0, $queue->ApproximateMessageCount);
    }

    /**
     * Test list queues
     */
    public function testListQueues()
    {
        $queueName1 = 'testlist1';
        $queueName2 = 'testlist2';
        $queueName3 = 'testlist3';
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName1);
        $storageClient->createQueue($queueName2);
        $storageClient->createQueue($queueName3);
        $result1 = $storageClient->listQueues('testlist');
        $result2 = $storageClient->listQueues('testlist', 1);

        // cleanup first
        $storageClient->deleteQueue($queueName1);
        $storageClient->deleteQueue($queueName2);
        $storageClient->deleteQueue($queueName3);

        $this->assertEquals(3, count($result1));
        $this->assertEquals($queueName2, $result1[1]->Name);

        $this->assertEquals(1, count($result2));
    }

    /**
     * Test put message
     */
    public function testPutMessage()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);
        $storageClient->putMessage($queueName, 'Test message', 120);

        sleep(45); // wait for the message to appear in the queue...

        $messages = $storageClient->getMessages($queueName);
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Test message', $messages[0]->MessageText);
    }

    /**
     * Test get messages
     */
    public function testGetMessages()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);
        $storageClient->putMessage($queueName, 'Test message 1', 120);
        $storageClient->putMessage($queueName, 'Test message 2', 120);
        $storageClient->putMessage($queueName, 'Test message 3', 120);
        $storageClient->putMessage($queueName, 'Test message 4', 120);

        sleep(45); // wait for the messages to appear in the queue...

        $messages1 = $storageClient->getMessages($queueName, 2);
        $messages2 = $storageClient->getMessages($queueName, 2);
        $messages3 = $storageClient->getMessages($queueName);

        $this->assertEquals(2, count($messages1));
        $this->assertEquals(2, count($messages2));
        $this->assertEquals(0, count($messages3));
    }

    /**
     * Test peek messages
     */
    public function testPeekMessages()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);
        $storageClient->putMessage($queueName, 'Test message 1', 120);
        $storageClient->putMessage($queueName, 'Test message 2', 120);
        $storageClient->putMessage($queueName, 'Test message 3', 120);
        $storageClient->putMessage($queueName, 'Test message 4', 120);

        sleep(45); // wait for the messages to appear in the queue...

        $messages1 = $storageClient->peekMessages($queueName, 4);
        $messages2 = $storageClient->getMessages($queueName, 4);

        $this->assertEquals(4, count($messages1));
        $this->assertEquals(4, count($messages2));
    }

    /**
     * Test clear messages
     */
    public function testClearMessages()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);
        $storageClient->putMessage($queueName, 'Test message 1', 120);
        $storageClient->putMessage($queueName, 'Test message 2', 120);
        $storageClient->putMessage($queueName, 'Test message 3', 120);
        $storageClient->putMessage($queueName, 'Test message 4', 120);

        sleep(45); // wait for the messages to appear in the queue...

        $messages1 = $storageClient->peekMessages($queueName, 4);
        $storageClient->clearMessages($queueName);

        sleep(45); // wait for the GC...

        $messages2 = $storageClient->peekMessages($queueName, 4);

        $this->assertEquals(4, count($messages1));
        $this->assertEquals(0, count($messages2));
    }

    /**
     * Test delete message
     */
    public function testDeleteMessage()
    {
        $queueName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createQueue($queueName);
        $storageClient->putMessage($queueName, 'Test message 1', 120);
        $storageClient->putMessage($queueName, 'Test message 2', 120);
        $storageClient->putMessage($queueName, 'Test message 3', 120);
        $storageClient->putMessage($queueName, 'Test message 4', 120);

        sleep(45); // wait for the messages to appear in the queue...

        $messages1 = $storageClient->getMessages($queueName, 2, 10);
        foreach ($messages1 as $message) {
            $storageClient->deleteMessage($queueName, $message);
        }

        sleep(45); // wait for the GC...

        $messages2 = $storageClient->getMessages($queueName, 4);

        $this->assertEquals(2, count($messages1));
        $this->assertEquals(2, count($messages2));
    }
}
