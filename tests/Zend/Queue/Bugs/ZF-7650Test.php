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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: QueueTest.php 17667 2009-08-18 21:40:09Z mikaelkael $
 */

/*
 * This code specifically tests for ZF-7650
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_Bugs_Zf7650Test extends PHPUnit_Framework_TestCase
{
    public function testArrayAdapterShouldReturnNoMessagesWhenZeroCountRequested()
    {
        // Zend_Queue_Adapter_Array
        $queue = new Zend_Queue('Array');
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));
    }

    public function testMemcacheqAdapterShouldReturnNoMessagesWhenZeroCountRequested()
    {
        if (!constant('TESTS_ZEND_QUEUE_MEMCACHEQ_ENABLED')) {
            $this->markTestSkipped('Zend_Queue Memcacheq adapter tests are not enabled');
        }
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_HOST')) {
            $driverOptions['host'] = TESTS_ZEND_QUEUE_MEMCACHEQ_HOST;
        }
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_PORT')) {
            $driverOptions['port'] = TESTS_ZEND_QUEUE_MEMCACHEQ_PORT;
        }
        $options = array('name' => 'ZF7650', 'driverOptions' => $driverOptions);

        $queue = new Zend_Queue('Memcacheq', $options);
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));

    }

    public function testDbAdapterShouldReturnNoMessagesWhenZeroCountRequested()
    {
        if (!constant('TESTS_ZEND_QUEUE_DB_ENABLED')) {
            $this->markTestSkipped('Zend_Queue DB adapter tests are not enabled');
        }
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_DB')) {
            $driverOptions = Zend_Json::decode(TESTS_ZEND_QUEUE_DB);
        }

        $options = array(
            'name'          => '/temp-queue/ZF7650',
            'options'       => array(Zend_Db_Select::FOR_UPDATE => true),
            'driverOptions' => $driverOptions,
        );

        $queue = new Zend_Queue('Db', $options);
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));
    }

    public function testActivemqAdapterShouldReturnNoMessagesWhenZeroCountRequested()
    {
        if (!constant('TESTS_ZEND_QUEUE_ACTIVEMQ_ENABLED')) {
            $this->markTestSkipped('Zend_Queue ActiveMQ adapter tests are not enabled');
        }
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_ACTIVEMQ_HOST')) {
            $driverOptions['host'] = TESTS_ZEND_QUEUE_ACTIVEMQ_HOST;
        }
        if (defined('TESTS_ZEND_QUEUE_ACTIVEMQ_PORT')) {
            $driverOptions['port'] = TESTS_ZEND_QUEUE_ACTIVEMQ_PORT;
        }
        if (defined('TESTS_ZEND_QUEUE_ACTIVEMQ_SCHEME')) {
            $driverOptions['scheme'] = TESTS_ZEND_QUEUE_ACTIVEMQ_SCHEME;
        }
        $options = array('driverOptions' => $driverOptions);

        $queue = new Zend_Queue('Activemq', $options);
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));
    }
}

