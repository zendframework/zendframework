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

/** TestHelp.php */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/** Zend_Queue_Adapter_Array */
require_once 'Zend/Queue/Adapter/Array.php';
/** Zend_Queue_Adapter_Null */
require_once 'Zend/Queue/Adapter/Null.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

class Zend_Queue_Message_IteratorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Test Zend_Config
        $this->options = array(
            'name'      => 'queue1',
            'params'    => array(),
        );

        $this->queue = new Zend_Queue('array', $this->options);

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
        if (!class_exists($classname)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($classname);
        }
        $this->messages = new $classname($options);
    }


    public function test_setup()
    {
        $this->assertTrue($this->queue instanceof Zend_Queue);
        $this->assertTrue(is_array($this->options));

        foreach ($this->messages as $i => $message) {
            $this->assertTrue($message instanceof Zend_Queue_Message);
            $this->assertEquals('Hello world', $message->body);
        }
    }

    protected function tearDown()
    {
    }

    public function testConstruct()
    {
        $this->assertTrue($this->messages instanceof Zend_Queue_Message_Iterator);

        // parameter validation
        try {
            $config = $this->options;
            $config['data']='ops';

            $classname = $this->queue->getMessageSetClass();
            Zend_Loader::loadClass($classname);
            $this->messages = new $classname($config);
            $this->fail('config[data] must be an array.  a message should have been thrown');
        } catch (Exception $e) {
            $this->assertTrue(true);
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
        $this->assertTrue($queue instanceof Zend_Queue);

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
