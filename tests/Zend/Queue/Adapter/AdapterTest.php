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
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
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

/** Zend_Queue_Message_Iterator2 */
require_once 'Iterator2.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */
abstract class Zend_Queue_Adapter_AdapterTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $this->error = false;
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You must overload this method
     *
     * @return string
     */
    public function getAdapterName()
    {
        die('You must overload this function: getAdapterName()');
        // example for Zend_Queue_Adatper_Array
        return 'Array';
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
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

    public function getTestConfig()
    {
        return array('driverOptions' => array());
    }

    /**
     * for ActiveMQ it uses /queue/ /temp-queue/ /topic/ /temp-topic/
     */
    public function createQueueName($name)
    {
        return $name;
    }

    /**
     * This is a generic function that creates a queue
     *
     * @param array $config, $config['name'] must be set.
     *
     * or
     *
     * @param string $name - name of the queue to create
     * @param array $config - a special config?
     * @return Zend_Queue
     */
    protected function createQueue($name, $config = null)
    {
        if (is_array($name)) {
            $config = $name;
        }

        if ($config === null) {
            $config = $this->getTestConfig();
            $config['name'] = $name;
        }

        if (is_string($name)) {
            $config['name'] = $name;
        }

        $config['name'] = $this->createQueueName($config['name']);

        $class = $this->getAdapterFullName();

        // create queue
        if (!class_exists($class)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($class);
        }

        set_error_handler(array($this, 'handleErrors'));
        try {
            $queue = new Zend_Queue($this->getAdapterName(), $config);
        } catch (Zend_Queue_Exception $e) {
            $this->markTestSkipped();
            restore_error_handler();
            return false;
        }
        restore_error_handler();

        return $queue;
    }

    public function handleErrors($errno, $errstr)
    {
        $this->error = true;
    }

    // test the constants
    public function testConst()
    {
        $this->markTestSkipped('must be tested in each individual adapter');
    }

    public function testGetOptions()
    {
        $config = $this->getTestConfig();
        $config['setting'] = true;

        if (!$queue = $this->createQueue(__FUNCTION__, $config)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $new = $adapter->getOptions();

        $this->assertTrue(is_array($new));
        $this->assertEquals($new['setting'], $config['setting']);

        // delete the queue we created
        $queue->deleteQueue();
    }

    // test the constructor
    public function testZendQueueAdapterConstructor()
    {
        $class = $this->getAdapterFullName();
        /**
         * @see Zend_Loader
         */
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass($class);

        try {
            $obj = new $class(true);
            $this->fail('__construct() $config must be an array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $obj = new $class( array());
            $this->fail('__construct() cannot accept an empty array for a configuration');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $obj = new $class(array('name' => 'queue1', 'driverOptions'=>true));
            $this->fail('__construct() $config[\'options\'] must be an array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $obj = new $class(array('name' => 'queue1', 'driverOptions'=>array('opt'=>'val')));
            $this->fail('__construct() humm I think this test is supposed to work @TODO');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $config = new Zend_Config(array('driverOptions' => array() ));
            $obj = new $class($config);
            $this->fail('__construct() \'name\' is a required configuration value');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $config = new Zend_Config(array('name' => 'queue1', 'driverOptions' => array(), 'options' => array('opt1' => 'val1')));
            $obj = new $class($config);
            $this->fail('__construct() is not supposed to accept a true value for a configuraiton');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        // try passing the queue to the $adapter
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $obj = new $class($queue->getOptions(), $queue);
        $this->assertTrue($obj instanceof Zend_Queue_Adapter_AdapterInterface);
    }

    // this tests the configuration option $config['messageClass']
    public function testZendQueueMessageTest()
    {
        $config = $this->getTestConfig();
        $config['messageClass'] = 'Zend_Queue_Message_Test';

        if (!$queue = $this->createQueue(__FUNCTION__, $config)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        if (! ($adapter->isSupported('send')
               && $adapter->isSupported('receive'))) {

            // delete the queue we created
            $queue->deleteQueue();

            $this->markTestSkipped('send() receive() are not supported');
        }

        $body = 'this is a test message';
        $message = $queue->send($body);

        $this->assertTrue($message instanceof Zend_Queue_Message);

        $list = $queue->receive();
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        foreach ( $list as $i => $message ) {
            $this->assertTrue($message instanceof Zend_Queue_Message_Test);
            $queue->deleteMessage($message);
        }

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testFactory()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $this->assertTrue($queue->getAdapter() instanceof Zend_Queue_Adapter_AdapterInterface);
    }

    public function testCreate()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'create';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        if ($adapter->isSupported('getQueues')) {
            $this->assertTrue(in_array($queue->getName(), $adapter->getQueues()));
        }

        // cannot recreate a queue.
        $this->assertFalse($adapter->create($queue->getName()));

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testDelete()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'delete';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $new = $this->createQueueName(__FUNCTION__ . '_2');
        $this->assertTrue($adapter->create($new));
        $this->assertTrue($adapter->delete($new));

        if ($adapter->isSupported('getQueues')) {
            if (in_array($new, $adapter->getQueues())) {
                $this->fail('delete() failed to delete it\'s queue, but returned true: '. $new);
            }
        }

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testIsExists()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'isExists';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $this->assertFalse($adapter->isExists('perl'));

        $new = $this->createQueueName(__FUNCTION__ . '_2');
        $this->assertTrue($adapter->create($new));
        $this->assertTrue($adapter->isExists($new));
        $this->assertTrue($adapter->delete($new));

        if ($adapter->isSupported('getQueues')) {
            if (in_array($new, $adapter->getQueues())) {
                $this->fail('delete() failed to delete it\'s queue, but returned true: '. $new);
            }
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

        // check to see if this function is supported
        $func = 'send';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        $body = 'this is a test message';
        $message = $adapter->send($body);
        $this->assertTrue($message instanceof Zend_Queue_Message);

        // receive the record we created.
        if (! $adapter->isSupported('receive')) {
            $messages = $adapter->receive();
            foreach ( $list as $i => $message ) {
                $this->assertTrue($message instanceof Zend_Queue_Message_Test);
                $queue->deleteMessage($message);
            }
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
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        // send the message
        $body = 'this is a test message 2';
        $message = $adapter->send($body);
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
        $this->assertEquals($message->body, $body);

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
        $func = 'deleteMessage';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        // in order to test this we need to send and receive so that the
        // test code can send a sample message.
        if (! ($adapter->isSupported('send') && $adapter->isSupported('receive'))) {
            $this->markTestSkipped('send() and receive() are not supported');
        }

        $body = 'this is a test message';
        $message = $adapter->send($body);
        $this->assertTrue($message instanceof Zend_Queue_Message);

        $list = $adapter->receive();
        $this->assertTrue($list instanceof Zend_Queue_Message_Iterator);
        $this->assertTrue($list->valid());

        $message = $list->current();
        $this->assertTrue($message instanceof Zend_Queue_Message);

        $this->assertTrue($adapter->deleteMessage($message));

        // no more messages, should return false
        // stomp and amazon always return true.
        $falsePositive = array('Activemq', 'Amazon');
        if (! in_array($this->getAdapterName(), $falsePositive)) {
            $this->assertFalse($adapter->deleteMessage($message));
        }

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testGetQueues()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        // check to see if this function is supported
        $func = 'getQueues';
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        // get a listing of queues
        $queues = $adapter->getQueues();

        // this is an array right?
        $this->assertTrue(is_array($queues));

        // make sure our current queue is in this list.
        $this->assertTrue(in_array($queue->getName(), $queues));

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
        if (! $adapter->isSupported($func)) {
            $this->markTestSkipped($func . '() is not supported');
            return;
        }

        // for a test case, the count should be zero at first.
        $this->assertEquals($adapter->count(), 0);
        if (! $adapter->isSupported('send') && $adapter->isSupported('receive') ) {
            $this->markTestSkipped('send() and receive() are not supported');
        }

        $body = 'this is a test message';

        // send a message
        $message = $adapter->send($body);

        // test queue count for being 1
        $this->assertEquals($adapter->count(), 1);

        // receive the message
        $message = $adapter->receive();

        /* we need to delete the messages we put in the queue before
         * counting.
         *
         * not all adapters support deleteMessage, but we should remove
         * the messages that we created if we can.
         */
        if ( $adapter->isSupported('deleteMessage') ) {
            foreach ( $message as $msg ) {
                $adapter->deleteMessage($msg);
            }
        }

        // test the count for being 0
        $this->assertEquals($adapter->count(), 0);

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testCapabilities()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $list = $adapter->getCapabilities();
        $this->assertTrue(is_array($list));

        // these functions must have an boolean answer
        $func = array(
            'create', 'delete', 'send', 'receive',
            'deleteMessage', 'getQueues', 'count',
            'isExists'
        );

        foreach ( array_values($func) as $f ) {
            $this->assertTrue(isset($list[$f]));
            $this->assertTrue(is_bool($list[$f]));
        }

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testIsSupported()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $list = $adapter->getCapabilities();
        foreach ( $list as $function => $result ) {
            $this->assertTrue(is_bool($result));
            if ( $result ) {
                $this->assertTrue($adapter->isSupported($function));
            } else {
                $this->assertFalse($adapter->isSupported($function));
            }
        }

        // delete the queue we created
        $queue->deleteQueue();
    }

    public function testGetQueue()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $this->assertTrue($queue === $queue->getAdapter()->getQueue());

        // delete the queue we created
        $queue->deleteQueue();
    }

    /*
     * Send about 10 messages, read 5 back, then read 5 back 1 at a time.
     * delete all messages and created queue
     */
    public function testSampleBehavior()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $this->assertTrue($queue instanceof Zend_Queue);

        if ($queue->isSupported('send')) {
            $msg = 1;

            for($i = 0; $i < 10; $i++) {
                $queue->send("$msg");
                $msg ++;
            }
        }

        if ($queue->isSupported('receive')) {
            $msg = 1;
            $messages = $queue->receive(5);

            foreach($messages as $i => $message) {
                $this->assertEquals($msg, $message->body);
                $queue->deleteMessage($message);
                $msg++;
            }

            for($i = 0; $i < 5; $i++) {
                $messages = $queue->receive();
                $message = $messages->current();
                $this->assertEquals($msg, $message->body);
                $queue->deleteMessage($message);
                $msg++;
            }
        }

        $this->assertEquals(0, count($queue));
        $this->assertTrue($queue->deleteQueue());

        // delete the queue we created
        $queue->deleteQueue();
    }

    /**
     * This tests to see if a message is in-visibile for the proper amount of time
     *
     * adapters that support deleteMessage() by nature will support visibility
     */
    public function testVisibility()
    {
        $debug = false;
        $default_timeout = 3; // how long we tell the queue to keep the message invisible
        $extra_delay = 2; // how long we are willing to wait for the test to finish before failing
        // keep in mind that some queue services are on forigen machines and need network time.

        if (false) { // easy comment/uncomment, set to true or false
            $this->markTestSkipped('Visibility testing takes ' . $default_timeout+$extra_delay . ' seconds per adapter, if you wish to test this, uncomment the test case in ' . __FILE__ . ' line ' . __LINE__);
            return;
        }

        $config = $this->getTestConfig();
        $config['timeout'] = 2;

        if (!$queue = $this->createQueue(__FUNCTION__, $config)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $not_supported = array('Activemq');
        if ((! $queue->isSupported('deleteMessage')) || in_array($this->getAdapterName(), $not_supported)) {
            $queue->deleteQueue();
            $this->markTestSkipped($this->getAdapterName() . ' does not support visibility of messages');
            return;
        }

        $body = 'hello world';

        $queue->send($body);
        $messages = $queue->receive(1); // messages are deleted at the bottom.

        if ($queue->isSupported('count')) {
            $this->assertEquals(1, count($queue));
        }

        $start = microtime(true);
        $end = 0;

        $this->assertTrue($messages instanceof Zend_Queue_Message_Iterator);

        $timeout = $config['timeout'] + $start + $extra_delay;
        $found = false;
        $check = microtime(true);

        $end = false;
        do {
            $search = $queue->receive(1);
            if ((microtime(true) - $check) > 0.1) {
                $check = microtime(true);
                if ($debug) echo "Checking - found ", count($search), " messages at : ", $check, "\n";
            }
            if ( count($search) > 0 ) {
                if ($search->current()->body == $body) {
                    $found = true;
                    $end = microtime(true);
                } else {
                    $this->fail('sent message is not the message received');
                }
            }
        } while ($found === false && microtime(true) < $timeout);

        // record end time
        if ($end === false) {
            $end = microtime(true);
        }

        $duration = sprintf("%5.2f seconds", $end-$start);
        /*
        There has to be some fuzzyness regarding comparisons because while
        the timeout may be honored, the actual code time, database querying
        and so on, may take more than the timeout time.
        */
        if ($found) {
            if (abs(($end-$start) - $config['timeout']) < $extra_delay) { // stupid Db Adapter responds in a fraction less than a second.
                $this->assertTrue(true, 'message was invisible for the required amount of time');
            } else {
                if ($debug) echo 'required duration of invisibility: ', $config['timeout'], ' seconds; actual duration: ', $duration, "\n";
                $this->fail('message was NOT invisible for the required amount of time');
            }
        } else {
            $this->fail('message never became visibile duration:' . $duration);
        }
        if ($debug) echo "duration $duration\n";

        // now we delete the messages
        if ( $adapter->isSupported('deleteMessage') ) {
            foreach ( $messages as $msg ) {
                $adapter->deleteMessage($msg);
            }
        }


        // delete the queue we created
        $queue->deleteQueue();
    }

    /**
     * tests a function for an exception
     *
     * @param string $func function name
     * @param array $args function arguments
     * @return boolean - true if exception, false if not
     */
    protected function try_exception($func, $args)
    {
        $return = false;

    }

    public function testIsSupportException()
    {
        if (!$queue = $this->createQueue(__FUNCTION__)) {
            return;
        }
        $adapter = $queue->getAdapter();

        $functions = $adapter->getCapabilities();

        if (! $functions['create']) {
            try {
                $adapter->create(__FUNCTION__ . '_2');
                $this->fail('unsupported create() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['delete']) {
            try {
                $adapter->delete(__FUNCTION__ . '_2');
                $this->fail('unsupported delete() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['send']) {
            try {
                $adapter->send(__FUNCTION__);
                $this->fail('unsupported send() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['receive']) {
            try {
                $adapter->send(__FUNCTION__);
                $this->fail('unsupported receive() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['receive']) {
            try {
                $adapter->receive();
                $this->fail('unsupported receive() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['deleteMessage']) {
            try {
                $message = new Zend_Queue_Message();
                $adapter->deleteMessage($message);
                $this->fail('unsupported deleteMessage() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['getQueues']) {
            try {
                $adapter->getQueues();
                $this->fail('unsupported getQueues() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['count']) {
            try {
                $a = $adapter->count();
                $this->fail('unsupported count() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        if (! $functions['isExists']) {
            try {
                $a = $adapter->isExists(__FUNCTION__ . '_3');
                $this->fail('unsupported isExists() failed to throw an exception');
            } catch (Exception $e) {
                $this->assertTrue(true, 'exception thrown');
            }
        }

        // delete the queue we created
        $queue->deleteQueue();
    }
}
