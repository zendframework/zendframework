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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Queue;
use Zend\Queue;
use Zend\Log;
use Zend\Log\Writer;
use Zend\Queue\Adapter;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Test Zend_Config
        $this->config = array(
            'name'      => 'queue1',
            'params'    => array(),
        );

        $this->queue = new Queue\Queue('ArrayAdapter', $this->config);
    }

    protected function tearDown()
    {
    }

    public function testConst()
    {
        $this->assertTrue(is_string(Queue\Queue::TIMEOUT));
        $this->assertTrue(is_integer(Queue\Queue::VISIBILITY_TIMEOUT));
        $this->assertTrue(is_string(Queue\Queue::NAME));
    }

    /**
     * Constructor
     *
     * @param string|Zend_Queue_Adapter_Abstract $adapter
     * @param array  $config
     */
    public function testConstruct()
    {
        // Test Zend_Config
        $config = array(
            'name'      => 'queue1',
            'params'    => array(),
            'adapter'   => 'ArrayAdapter'
        );

        $zend_config = new \Zend\Config\Config($config);

        $obj = new Queue\Queue($config);
        $this->assertTrue($obj instanceof Queue\Queue);

        $obj = new Queue\Queue($zend_config);
        $this->assertTrue($obj instanceof Queue\Queue);
    }

    public function test_getConfig()
    {
        $options = $this->queue->getOptions();
        $this->assertTrue(is_array($options));
        $this->assertEquals($this->config['name'], $options['name']);
    }

    public function test_set_getAdapter()
    {
        $adapter = new Adapter\ArrayAdapter($this->config);
        $this->assertTrue($this->queue->setAdapter($adapter) instanceof Queue\Queue);
        $this->assertTrue($this->queue->getAdapter($adapter) instanceof Adapter\ArrayAdapter);
    }

    public function test_set_getMessageClass()
    {
        $class = 'test';
        $this->assertTrue($this->queue->setMessageClass($class) instanceof Queue\Queue);
        $this->assertEquals($class, $this->queue->getMessageClass());
    }

    public function test_set_getMessageSetClass()
    {
        $class = 'test';
        $this->assertTrue($this->queue->setMessageSetClass($class) instanceof Queue\Queue);
        $this->assertEquals($class, $this->queue->getMessageSetClass());
    }

    public function test_set_getName()
    {
        // $this->assertTrue($this->queue->setName($new) instanceof Zend_Queue);
        $this->assertEquals($this->config['name'], $this->queue->getName());
    }

    public function test_create_deleteQueue()
    {
        // parameter testing
        try {
            $this->queue->createQueue(array());
            $this->fail('createQueue() $name must be a string');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->queue->createQueue('test', 'test');
            $this->fail('createQueue() $timeout must be an integer');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        // isExists
        $queue = 'test';
        $new = $this->queue->createQueue($queue);
        $this->assertTrue($new instanceof Queue\Queue);
        $this->assertFalse($this->queue->createQueue($queue));

        $this->assertTrue($new->deleteQueue());
    }

    public function test_send_count_receive_deleteMessage()
    {
        // ------------------------------------ send()
        // parameter verification
        try {
            $this->queue->send(array());
            $this->fail('send() $mesage must be a string');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $message = 'Hello world'; // never gets boring!
        $this->assertTrue($this->queue->send($message) instanceof \Zend\Queue\Message);

        // ------------------------------------ count()
        $this->assertEquals($this->queue->count(), 1);

        // ------------------------------------ receive()
        // parameter verification
        try {
            $this->queue->receive(array());
            $this->fail('receive() $maxMessages must be a integer or null');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->queue->receive(1, array());
            $this->fail('receive() $timeout must be a integer or null');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $messages = $this->queue->receive();
        $this->assertTrue($messages instanceof \Zend\Queue\Message\MessageIterator);

        // ------------------------------------ deleteMessage()
        foreach ($messages as $i => $message) {
            $this->assertTrue($this->queue->deleteMessage($message));
        }
    }

/*
    public function test_set_getLogger()
    {
        $logger = new Log\Logger(new Writer\Null);

        $this->assertTrue($this->queue->setLogger($logger) instanceof Queue\Queue);
        $this->assertTrue($this->queue->getLogger() instanceof Log\Logger);

        // parameter verification
        try {
            $this->queue->setLogger(array());
            $this->fail('setlogger() passed an array and succeeded (bad)');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
*/

    public function test_capabilities()
    {
        $list = $this->queue->getCapabilities();
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
    }

    public function test_isSupported()
    {
        $list = $this->queue->getCapabilities();
        foreach ( $list as $function => $result ) {
            $this->assertTrue(is_bool($result));
            if ( $result ) {
                $this->assertTrue($this->queue->isSupported($function));
            } else {
                $this->assertFalse($this->queue->isSupported($function));
            }
        }
    }

    public function test_getQueues()
    {
        $queues = $this->queue->getQueues();
        $this->assertTrue(is_array($queues));
        $this->assertTrue(in_array($this->config['name'], $queues));
    }
}
