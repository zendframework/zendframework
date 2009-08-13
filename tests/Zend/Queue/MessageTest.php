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
require_once dirname(__FILE__) . '/../../TestHelper.php';

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
 * @group      Zend_Queue
 */
class Zend_Queue_MessageTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Test Zend_Config
        $this->options = array(
            'name'      => 'queue1',
            'params'    => array(),
        );

        $this->queue = new Zend_Queue('array', $this->options);

        $this->data = array(
            'id'     => 123,
            'handle' => 567,
            'body'   => 'Hello world' // This is my 2524'th time writing that.
        );

        $this->options = array(
            'queue'     => $this->queue,
            'data'      => $this->data,
        );

        $this->message = new Zend_Queue_Message($this->options);
    }

    protected function tearDown()
    {
    }

    public function testConstruct()
    {
        try {
            $message = new Zend_Queue_Message($this->options);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail('should have gotten a valid object');
        }

        // parameter verification
        try {
            $config2 = $this->options;
            $config2['queue'] = 'weee';
            $message = new Zend_Queue_Message($config2);
            $this->fail('should have thrown an exception bad queue var');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $config2 = $this->options;
            $config2['data'] = 'weee';
            $message = new Zend_Queue_Message($config2);
            $this->fail('should have thrown an exception bad queue var');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testMagic()
    {
        $this->assertEquals(123, $this->message->__get('id'));
        $this->assertEquals(123, $this->message->id);
        $this->assertEquals('Hello world', $this->message->body);
        $this->message->__set('id', 'abc');
        $this->assertEquals('abc', $this->message->__get('id'));
        $this->assertTrue($this->message->__isset('id'));

        try {
            $this->message->__get('hello world');
            $this->fail('key is NOT in variable, should have thrown an exception');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->message->__set('hello world', 'good bye');
            $this->fail('key is NOT in variable, should have thrown an exception');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $message = new Zend_Queue_Message($this->options);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail('should have gotten a valid object');
        }

        // parameter verification
        try {
            $config2 = $this->options;
            $config2['queue'] = 'weee';
            $message = new Zend_Queue_Message($config2);
            $this->fail('should have thrown an exception bad queue var');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $config2 = $this->options;
            $config2['data'] = 'weee';
            $message = new Zend_Queue_Message($config2);
            $this->fail('should have thrown an exception bad queue var');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_set_getQueue()
    {
        $this->assertTrue($this->message->getQueue() instanceof Zend_Queue);

        $class = $this->message->getQueueClass();
        $this->assertEquals('Zend_Queue', $class);

        $this->assertTrue($this->message->setQueue($this->message->getQueue()));

        // parameter verification

        try {
            $null = new Zend_Queue('Null', array());
            $this->message->setQueue($null);
            $this->fail('invalid class passed to setQueue()');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_array()
    {
        $array = $this->message->toArray();
        $this->assertTrue(is_array($array));

        $array['id'] = 'hello';
        $this->message->setFromArray($array);

        $this->assertEquals('hello', $this->message->id);
    }

    public function test_magic()
    {
        $this->assertTrue(is_array($this->message->__sleep()));

        $message = serialize($this->message);
        $woken = unserialize($message);
        $this->assertEquals($this->message->body, $woken->body);
    }

}
