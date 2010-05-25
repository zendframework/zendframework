<?php
/**
 * Phly - PHp LibrarY
 * 
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (C) 2008 - Present, Matthew Weier O'Phinney
 * @author     Matthew Weier O'Phinney <mweierophinney@gmail.com> 
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace ZendTest\SignalSlot;
use Zend\SignalSlot\FilterChain,
    Zend\SignalSlot\Slot;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->filterchain = new FilterChain;
    }

    public function testSubscribeShouldReturnSlot()
    {
        $handle = $this->filterchain->connect($this, __METHOD__);
        $this->assertTrue($handle instanceof Slot);
    }

    public function testSubscribeShouldAddSlotToSubscribers()
    {
        $handler  = $this->filterchain->connect($this, __METHOD__);
        $handlers = $this->filterchain->getFilters();
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handler, $handlers);
    }

    public function testUnsubscribeShouldRemoveSlotFromSubscribers()
    {
        $handle = $this->filterchain->connect($this, __METHOD__);
        $handles = $this->filterchain->getFilters();
        $this->assertContains($handle, $handles);
        $this->filterchain->detach($handle);
        $handles = $this->filterchain->getFilters();
        $this->assertNotContains($handle, $handles);
    }

    public function testUnsubscribeShouldReturnFalseIfSlotDoesNotExist()
    {
        $handle1 = $this->filterchain->connect($this, __METHOD__);
        $this->filterchain->clearFilters();
        $handle2 = $this->filterchain->connect($this, 'handleTestTopic');
        $this->assertFalse($this->filterchain->detach($handle1));
    }

    public function testRetrievingSubscribedSlotsShouldReturnEmptyArrayWhenNoSubscribersExist()
    {
        $handles = $this->filterchain->getFilters();
        $this->assertTrue(empty($handles));
    }

    public function testFilterShouldPassReturnValueOfEachSubscriberToNextSubscriber()
    {
        $this->filterchain->connect('trim');
        $this->filterchain->connect('str_rot13');
        $value = $this->filterchain->filter(' foo ');
        $this->assertEquals(\str_rot13('foo'), $value);
    }

    public function testFilterShouldAllowMultipleArgumentsButFilterOnlyFirst()
    {
        $this->filterchain->connect($this, 'filterTestCallback1');
        $this->filterchain->connect($this, 'filterTestCallback2');
        $obj = (object) array('foo' => 'bar', 'bar' => 'baz');
        $value = $this->filterchain->filter('', $obj);
        $this->assertEquals('foo:bar;bar:baz;', $value);
        $this->assertEquals((object) array('foo' => 'bar', 'bar' => 'baz'), $obj);
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }

    public function filterTestCallback1($string, $object)
    {
        if (isset($object->foo)) {
            $string .= 'foo:' . $object->foo . ';';
        }
        return $string;
    }

    public function filterTestCallback2($string, $object)
    {
        if (isset($object->bar)) {
            $string .= 'bar:' . $object->bar . ';';
        }
        return $string;
    }
}
