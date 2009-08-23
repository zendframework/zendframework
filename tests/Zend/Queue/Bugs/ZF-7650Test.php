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
 * @version    $Id: QueueTest.php 17667 2009-08-18 21:40:09Z mikaelkael $
 */

/*
 * This code specifically tests for ZF-7650
 */

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** TestHelp.php */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/** Zend_Queue_Adapter_Array */
require_once 'Zend/Queue/Adapter/Array.php';

/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_QueueTest extends PHPUnit_Framework_TestCase
{
    public function test_ZF_7650()
    {
        // Zend_Queue_Adapter_Array
        $queue = new Zend_Queue('Array');
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));

        // Zend_Queue_Adapter_Memcacheq
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

        // Zend_Queue_Adapter_Db
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_DB')) {
            require_once 'Zend/Json.php';
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

        // Zend_Queue_Adapter_Activemq
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

