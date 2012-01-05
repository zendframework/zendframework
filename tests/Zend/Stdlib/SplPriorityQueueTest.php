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
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\SplPriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SplPriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->queue = new SplPriorityQueue();
        $this->queue->insert('foo', 3);
        $this->queue->insert('bar', 4);
        $this->queue->insert('baz', 2);
        $this->queue->insert('bat', 1);
    }

    public function testMaintainsInsertOrderForDataOfEqualPriority()
    {
        $queue = new SplPriorityQueue();
        $queue->insert('foo', 1000);
        $queue->insert('bar', 1000);
        $queue->insert('baz', 1000);
        $queue->insert('bat', 1000);

        $expected = array('foo', 'bar', 'baz', 'bat');
        $test     = array();
        foreach ($queue as $datum) {
            $test[] = $datum;
        }
        $this->assertEquals($expected, $test);
    }

    public function testSerializationAndDeserializationShouldMaintainState()
    {
        $s = serialize($this->queue);
        $unserialized = unserialize($s);
        $count = count($this->queue);
        $this->assertSame($count, count($unserialized), 'Expected count ' . $count . '; received ' . count($unserialized));

        $expected = array();
        foreach ($this->queue as $item) {
            $expected[] = $item;
        }
        $test = array();
        foreach ($unserialized as $item) {
            $test[] = $item;
        }
        $this->assertSame($expected, $test, 'Expected: ' . var_export($expected, 1) . "\nReceived:" . var_export($test, 1));
    }

    public function testCanRetrieveQueueAsArray()
    {
        $expected = array(
            'bar', 
            'foo', 
            'baz', 
            'bat',
        );
        $test     = $this->queue->toArray();
        $this->assertSame($expected, $test, var_export($test, 1));
    }
}
