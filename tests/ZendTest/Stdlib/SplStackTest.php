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

use Zend\Stdlib\SplStack;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 */
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
