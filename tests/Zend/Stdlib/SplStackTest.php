<?php
namespace ZendTest\Stdlib;

use Zend\Stdlib\SplStack;

class SplStackTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->stack = new SplStack();
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->stack->push('baz');
        $this->stack->push('bat');
    }

    public function testSerializationAndDeserializationShouldMaintainState()
    {
        $s = serialize($this->stack);
        $unserialized = unserialize($s);
        $count = count($this->stack);
        $this->assertSame($count, count($unserialized));

        $expected = array();
        foreach ($this->stack as $item) {
            $expected[] = $item;
        }
        $test = array();
        foreach ($unserialized as $item) {
            $test[] = $item;
        }
        $this->assertSame($expected, $test);
    }

    public function testCanRetrieveQueueAsArray()
    {
        $expected = array('bat', 'baz', 'bar', 'foo');
        $test     = $this->stack->toArray();
        $this->assertSame($expected, $test, var_export($test, 1));
    }
}
