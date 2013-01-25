<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\PriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 */
class PriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriorityQueue
     */
    protected $queue;

    public function setUp()
    {
        $this->queue = new PriorityQueue();
        $this->queue->insert('foo', 3);
        $this->queue->insert('bar', 4);
        $this->queue->insert('baz', 2);
        $this->queue->insert('bat', 1);
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

    public function testRetrievingQueueAsArrayReturnsDataOnlyByDefault()
    {
        $expected = array(
            'foo',
            'bar',
            'baz',
            'bat',
        );
        $test     = $this->queue->toArray();
        $this->assertSame($expected, $test, var_export($test, 1));
    }

    public function testCanCastToArrayOfPrioritiesOnly()
    {
        $expected = array(
            3,
            4,
            2,
            1,
        );
        $test     = $this->queue->toArray(PriorityQueue::EXTR_PRIORITY);
        $this->assertSame($expected, $test, var_export($test, 1));
    }

    public function testCanCastToArrayOfDataPriorityPairs()
    {
        $expected = array(
            array('data' => 'foo', 'priority' => 3),
            array('data' => 'bar', 'priority' => 4),
            array('data' => 'baz', 'priority' => 2),
            array('data' => 'bat', 'priority' => 1),
        );
        $test     = $this->queue->toArray(PriorityQueue::EXTR_BOTH);
        $this->assertSame($expected, $test, var_export($test, 1));
    }

    public function testCanIterateMultipleTimesAndReceiveSameResults()
    {
        $expected = array('bar', 'foo', 'baz', 'bat');

        for ($i = 1; $i < 3; $i++) {
            $test = array();
            foreach ($this->queue as $item) {
                $test[] = $item;
            }
            $this->assertEquals($expected, $test, 'Failed at iteration ' . $i);
        }
    }

    public function testCanRemoveItemFromQueue()
    {
        $this->queue->remove('baz');
        $expected = array('bar', 'foo', 'bat');
        $test = array();
        foreach ($this->queue as $item) {
            $test[] = $item;
        }
        $this->assertEquals($expected, $test);
    }

    public function testCanTestForExistenceOfItemInQueue()
    {
        $this->assertTrue($this->queue->contains('foo'));
        $this->assertFalse($this->queue->contains('foobar'));
    }

    public function testCanTestForExistenceOfPriorityInQueue()
    {
        $this->assertTrue($this->queue->hasPriority(3));
        $this->assertFalse($this->queue->hasPriority(1000));
    }

    public function testCloningAlsoClonesQueue()
    {
        $foo  = new \stdClass();
        $foo->name = 'bar';

        $queue = new PriorityQueue();
        $queue->insert($foo, 1);
        $queue->insert($foo, 2);

        $queueClone = clone $queue;

        while (!$queue->isEmpty()) {
            $this->assertSame($foo, $queue->top());
            $queue->remove($queue->top());
        }

        $this->assertTrue($queue->isEmpty());
        $this->assertFalse($queueClone->isEmpty());
        $this->assertEquals(2, $queueClone->count());

        while (!$queueClone->isEmpty()) {
            $this->assertSame($foo, $queueClone->top());
            $queueClone->remove($queueClone->top());
        }

        $this->assertTrue($queueClone->isEmpty());
    }
}
