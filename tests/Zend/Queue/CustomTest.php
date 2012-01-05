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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Queue;
use Zend\Queue;

/*
 * This test code tests the customization functions provided in the example
 * documentation code.
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class CustomTest extends \PHPUnit_Framework_TestCase
{
    public function test_behavior()
    {
        $object_count = 10;
        $objects = array();

        $queue = new Custom\Queue('ArrayAdapter', array('name'=>'ObjectA'));
        $this->assertTrue($queue instanceof Custom\Queue);

        // ------------------------------------------------ send

        // add items $objects[0-4]
        $objects = array();
        for ($i = 0; $i < $object_count-5; $i++) {
            $object = new CustomObject();
            $queue->send(new Custom\Message($object));
            $objects[] = $object;
        }

        // add items $objects[5-9]
        $messages = new Custom\Messages();
        for ($i = 0; $i < 5; $i++) {
            $object = new CustomObject();
            $messages->append( new Custom\Message($object));
            $objects[] = $object;
        }
        $queue->send($messages);

        $this->assertEquals($object_count, count($queue));
        unset($messages);

        // ------------------------------------------------ receive

        // get the first 5 doing 0-4
        $receive = $queue->receive(5);
        $this->assertTrue($receive instanceof Custom\Messages);
        $this->assertEquals(5, count($receive));

        // test them
        for ($index = 0; $index < 5; $index++) {
            $this->assertEquals($objects[$index]->getA(), $receive[$index]->getBody()->getA());
            try {
                unset($receive[$index]);
                $this->assertTrue(true, '$receive[$index] successfully deleted');
            } catch(Queue\Exception $e) {
                $this->fail('$receive[$index] should have been deleted' . $e->getMessage());
            }
        }
        // there should only be 5 objects left
        $this->assertEquals($object_count - $index, count($queue));

        // get 1 doing $objects[5]
        $receive = $queue->receive();
        $index++;
        $this->assertTrue($receive instanceof Custom\Messages);
        $this->assertEquals(1, count($receive));

        // testing Custom\Messages::__deconstruct()
        unset($receive);
        $this->assertEquals($object_count - $index, count($queue));


        // get all the rest doing 6-20
        $receive = $queue->receive($object_count - $index);
        $this->assertTrue($receive instanceof Custom\Messages);
        $this->assertEquals($object_count - $index, count($receive));

        // test them
        $r_index = -1;
        for (; $index < $object_count; $index++) {
            $r_index++;
            $this->assertEquals($objects[$index]->getA(), $receive[$r_index]->getBody()->getA());

            try {
                unset($receive[$r_index]);
                $this->assertTrue(true, '$receive[$index] successfully deleted');
            } catch(Queue\Exception $e) {
                $this->fail('$receive[$index] should have been deleted' . $e->getMessage());
            }
        }

        // auto-delete should have been called on $receive
        $this->assertEquals(0, count($queue));
    }
}

class CustomObject {
    public $a;

    public function __construct()
    {
        $a = rand(1,200);
    }

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function __sleep()
    {
        return array('a'); // serialize only this variable
    }
}

