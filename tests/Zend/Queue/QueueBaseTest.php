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

/** TestHelper.php */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Config */
require_once 'Zend/Config.php';

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/** Zend_Queue_Adapter_Array */
require_once 'Zend/Queue/Adapter/Array.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
abstract class Zend_Queue_QueueBaseTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Test Zend_Config
        $this->config = array(
            'name'      => 'queue1',
        );

        $this->queue = new Zend_Queue('Null', $this->config);
    }

    protected function tearDown()
    {
    }

    public function testConst()
    {
        $this->assertTrue(is_string(Zend_Queue::TIMEOUT));
        $this->assertTrue(is_integer(Zend_Queue::VISIBILITY_TIMEOUT));
        $this->assertTrue(is_string(Zend_Queue::NAME));
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
            'adapter'   => 'array'
        );

        $zend_config = new Zend_Config($config);

        $obj = new Zend_Queue($config);
        $this->assertTrue($obj instanceof Zend_Queue);

        $obj = new Zend_Queue($zend_config);
        $this->assertTrue($obj instanceof Zend_Queue);

        try {
            $obj = new Zend_Queue('ops');
            $this->fail('Zend_Queue cannot accept a string');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testDebugInfo()
    {
        $this->assertTrue(is_array($this->queue->debugInfo()));
        // var_dump($this->queue->debugInfo());
    }

    public function testGetOptions()
    {
        $options = $this->queue->getOptions();
        $this->assertTrue(is_array($options));
        $this->assertEquals($this->config['name'], $options['name']);
    }

    public function testSetAndGetAdapter()
    {
        $adapter = new Zend_Queue_Adapter_Array($this->config);
        $this->assertTrue($this->queue->setAdapter($adapter) instanceof Zend_Queue);
        $this->assertTrue($this->queue->getAdapter($adapter) instanceof Zend_Queue_Adapter_Array);
    }

    public function testSetAndGetMessageClass()
    {
        $class = 'test';
        $this->assertTrue($this->queue->setMessageClass($class) instanceof Zend_Queue);
        $this->assertEquals($class, $this->queue->getMessageClass());
    }

    public function testSetAndGetMessageSetClass()
    {
        $class = 'test';
        $this->assertTrue($this->queue->setMessageSetClass($class) instanceof Zend_Queue);
        $this->assertEquals($class, $this->queue->getMessageSetClass());
    }

    public function testSetAndGetName()
    {
        $this->assertEquals($this->config['name'], $this->queue->getName());
    }

    public function testCreateAndDeleteQueue()
    {
        // parameter testing
        try {
            $this->queue->createQueue(array());
            $this->fail('createQueue() $name must be a string');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->queue->createQueue('test', 'test');
            $this->fail('createQueue() $timeout must be an integer');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        // isExists
        $queue = 'test';
        $new = $this->queue->createQueue($queue);
        $this->assertTrue($new instanceof Zend_Queue);

        // createQueue() will return true if the adapter cannot
        // do isExist($queue);
        // $this->assertFalse($this->queue->createQueue($queue));

        if ($new->isSupported('deleteQueue')) {
            $this->assertTrue($new->deleteQueue());
        }
    }

    public function testSendAndCountAndReceiveAndDeleteMessage()
    {
        if (! $this->queue->isSupported('send')
            && ! $this->queue->isSupported('receive')
            && ! $this->queue->isSupported('count')) {
            $this->markTestSkipped('send/count/receive are not supported');
            return;
        }

        // ------------------------------------ send()
        // parameter verification
        try {
            $this->queue->send(array());
            $this->fail('send() $mesage must be a string');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $message = 'Hello world'; // never gets boring!
        $this->assertTrue($this->queue->send($message) instanceof Zend_Queue_Message);

        // ------------------------------------ count()
        $this->assertEquals($this->queue->count(), 1);

        // ------------------------------------ receive()
        // parameter verification
        try {
            $this->queue->receive(array());
            $this->fail('receive() $maxMessages must be a integer or null');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->queue->receive(1, array());
            $this->fail('receive() $timeout must be a integer or null');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $messages = $this->queue->receive();
        $this->assertTrue($messages instanceof Zend_Queue_Message_Iterator);

        // ------------------------------------ deleteMessage()
        foreach ($messages as $i => $message) {
            $this->assertTrue($this->queue->deleteMessage($message));
        }
    }

    public function testCapabilities()
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

    public function testIsSupported()
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

    public function testGetQueues()
    {
        if ($this->queue->isSupported('getQueues')) {
            $queues = $this->queue->getQueues();
            $this->assertTrue(is_array($queues));
            $this->assertTrue(in_array($this->config['name'], $queues));
        } else {
            try {
                $queues = $this->queue->getQueues();
                $this->fail('getQueues() should have thrown an error');
            } catch (Exception $e) {
                $this->assertTrue(true);
            }
        }
    }
}
