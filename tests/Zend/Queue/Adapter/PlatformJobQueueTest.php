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
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** TestHelp.php */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/** Zend_Queue_Message_Test */
require_once 'MessageTestClass.php';

/** Base Adapter test class */
require_once dirname(__FILE__) . '/AdapterTest.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_Adapter_PlatformJobQueueTest extends Zend_Queue_Adapter_AdapterTest
{
    public function setUp()
    {
        if (!defined('TESTS_ZEND_QUEUE_PLATFORMJQ_HOST') 
            || !constant('TESTS_ZEND_QUEUE_PLATFORMJQ_HOST')
        ) {
            $this->markTestSkipped();
        }
    }

    /**
     * getAdapterName() is a method to help make AdapterTest work with any
     * new adapters
     *
     * You must overload this method
     *
     * @return string
     */
    public function getAdapterName()
    {
        return 'PlatformJobQueue';
    }

    public function getTestConfig()
    {
        return array('daemonOptions' => array(
            'host'     => constant('TESTS_ZEND_QUEUE_PLATFORMJQ_HOST'),
            'password' => constant('TESTS_ZEND_QUEUE_PLATFORMJQ_PASS'),
        ));
    }
    
    /**
     * getAdapterFullName() is a method to help make AdapterTest work with any
     * new adapters
     *
     * You may overload this method.  The default return is
     * 'Zend_Queue_Adapter_' . $this->getAdapterName()
     *
     * @return string
     */
    public function getAdapterFullName()
    {
        return 'Zend_Queue_Adapter_' . $this->getAdapterName();
    }

    public function testFailedConstructor()
    {
      try {
            $queue = $this->createQueue(__FUNCTION__, array());
            $this->fail('The test should fail if no host and password are passed');
        } catch (Exception $e) {
            $this->assertTrue( true, 'Job Queue host and password should be provided');
        }
        
        try {
            $queue = $this->createQueue(__FUNCTION__, array('daemonOptions' => array()));
            $this->fail('The test should fail if no host is passed');
        } catch (Exception $e) {
            $this->assertTrue(true, 'Platform Job Queue host should be provided');
        }

        try {
            $queue = $this->createQueue(__FUNCTION__, array('daemonOptions' => array('host' => 'localhost')));
            $this->fail('The test should fail if no password is passed');
        } catch (Exception $e) {
            $this->assertTrue(true, 'Platform Job Queue password should be provided');
        }
    }
    
    // this tests the configuration option $config['messageClass']
    public function testZendQueueMessageTest()
    {
        $config = $this->getTestConfig();

        if (!$queue = $this->createQueue(__FUNCTION__, $config)) {
            return;
        }

        $message = $queue->send(array('script' => 'info.php'));

        $this->assertTrue($message instanceof Zend_Queue_Message);

        $list = $queue->receive();
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        foreach ( $list as $message ) {
            $this->assertTrue($message instanceof Zend_Queue_Message_PlatformJob);
            $queue->deleteMessage($message);
        }

        // delete the queue we created
        $queue->deleteQueue();
    }
    
    public function testSend()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $message = $adapter->send(array('script' => 'info.php'));
        $this->assertTrue($message instanceof Zend_Queue_Message);

        $list = $queue->receive();
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        
        foreach ($list as $message) {
            $this->assertTrue($message instanceof Zend_Queue_Message_PlatformJob);
            $queue->deleteMessage($message);
        }

        // delete the queue we created
        $queue->deleteQueue();
        
    }
    
    public function testReceive()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'receive';
        if (!$adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $scriptName = 'info.php';
        
        // send the message
        $message = $adapter->send((array('script' => $scriptName)));
        $this->assertTrue($message instanceof Zend_Queue_Message);

        // get it back
        $list = $adapter->receive(1);
        $this->assertEquals(1, count($list));
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        $this->assertTrue($list->valid());

        $message = $list->current();
        if ($adapter->isSupported('deleteMessage')) {
            $adapter->deleteMessage($list->current());
        }

        $this->assertTrue($message instanceof Zend_Queue_Message);
        $this->assertEquals($message->getJob()->getScript(), $scriptName);

        // delete the queue we created
        $queue->deleteQueue();
    }
    
    public function testDeleteMessage()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'receive';
        if (!$adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $scriptName = 'info.php';
        
        // send the message
        $message = $adapter->send((array('script' => $scriptName)));
        $this->assertTrue($message instanceof Zend_Queue_Message);

        // get it back
        $list = $adapter->receive(1);
        $this->assertEquals(1, count($list));
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        $this->assertTrue($list->valid());

        $message = $list->current();
        if ($adapter->isSupported('deleteMessage')) {
            $adapter->deleteMessage($list->current());
        }

        $this->assertTrue($message instanceof Zend_Queue_Message);
        $this->assertEquals($message->getJob()->getScript(), $scriptName);
        
        $id = $message->getJob()->getID();
        $this->assertFalse($adapter->isJobIdExist($id));

        // delete the queue we created
        $queue->deleteQueue();
        
    }
    
     public function testCount()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'count';
        if (!$adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $initCount = $adapter->count();
      
        // send a message
        $message = $adapter->send(array('script' => 'info.php'));

        // test queue count for being 1
        $this->assertEquals($adapter->count(), ($initCount + 1));

        // receive the message
        $message = $adapter->receive();

        /* we need to delete the messages we put in the queue before
         * counting.
         *
         * not all adapters support deleteMessage, but we should remove
         * the messages that we created if we can.
         */
        if ($adapter->isSupported('deleteMessage')) {
            foreach ($message as $msg) {
                $adapter->deleteMessage($msg);
            }
        }

        // test the count for being 0
        $this->assertEquals($adapter->count(), $initCount);

        // delete the queue we created
        $queue->deleteQueue();
    }
    
    public function testSampleBehavior()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $this->assertTrue($queue instanceof Zend_Queue);

        $initCount = $queue->count();
         
        $scriptName = 'info.php';
        
        for ($i = 0; $i < 10; $i++) {
            $queue->send(array('script' => $scriptName));
        }

        $messages = $queue->receive(5);

        foreach($messages as $i => $message) {
            $this->assertEquals($message->getJob()->getScript(), $scriptName);
            $queue->deleteMessage($message);
        }

        for ($i = 0; $i < 5; $i++) {
            $messages = $queue->receive();
            $message  = $messages->current();
            $queue->deleteMessage($message);
        }

        $this->assertEquals($initCount, count($queue));
        $this->assertTrue($queue->deleteQueue());

        // delete the queue we created
        $queue->deleteQueue();
    }
    
    public function testVisibility() 
    {
        $this->markTestSkipped('testVisibility unsupported');
    }
    
    // test the constants
    public function testConst()
    {
        $this->markTestSkipped('no constants to test');
    }
}
