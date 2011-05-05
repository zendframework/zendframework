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
namespace ZendTest\Queue\Message;
use Zend\Queue;

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
class IteratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Test Zend_Config
        $this->options = array(
            'name'      => 'queue1',
            'params'    => array(),
        );

        $this->queue = new Queue\Queue('ArrayAdapter', $this->options);

        // construct messages
        $this->message_count = 5;
        $data  = array();
        $datum = array();
        for ($i = 0; $i < $this->message_count; $i++) {
            $data[] = array(
                'id' => $i+1,
                'handle' => null,
                'body' => 'Hello world' // This is my 2524'th time writing that.
            );
        }

        $options = array(
            'queue'    => $this->queue,
            'data'     => $data,
            'messageClass' => $this->queue->getMessageClass()
        );

        $classname = $this->queue->getMessageSetClass();
        $this->messages = new $classname($options);
    }


    public function test_setup()
    {
        $this->assertTrue($this->queue instanceof Queue\Queue);
        $this->assertTrue(is_array($this->options));

        foreach ($this->messages as $i => $message) {
            $this->assertTrue($message instanceof \Zend\Queue\Message);
            $this->assertEquals('Hello world', $message->body);
        }
    }

    public function testConstruct()
    {
        $this->assertTrue($this->messages instanceof \Zend\Queue\Message\MessageIterator);

        // parameter validation
        try {
            $config = $this->options;
            $config['data']='ops';

            $classname = $this->queue->getMessageSetClass();
            $this->messages = new $classname($config);
            $this->fail('config[data] must be an array. An exception should have been thrown.');
        } catch (\Exception $e) {
            // Exception is expected, do nothing
        }
    }

    public function test_count()
    {
        $this->assertEquals($this->message_count, count($this->messages));
    }

    public function test_magic()
    {
        $this->assertTrue(is_array($this->messages->__sleep()));

        $messages = serialize($this->messages);
        $woken = unserialize($messages);
        $this->assertEquals($this->messages->current()->body, $woken->current()->body);
    }

    public function test_get_setQueue()
    {
        $queue = $this->messages->getQueue();
        $this->assertTrue($queue instanceof Queue\Queue);

        $this->assertTrue($this->messages->setQueue($queue));
    }

    public function test_getQueueClass()
    {
        $this->assertEquals(get_class($this->queue), $this->messages->getQueueClass());
    }

    public function test_iterator()
    {
        foreach ($this->messages as $i => $message) {
            $this->assertEquals('Hello world', $message->body);
        }
    }

    public function test_toArray()
    {
        $array = $this->messages->toArray();
        $this->assertTrue(is_array($array));
        $this->assertEquals($this->message_count, count($array));
        $this->assertEquals('Hello world', $array[0]['body']);
    }

}
