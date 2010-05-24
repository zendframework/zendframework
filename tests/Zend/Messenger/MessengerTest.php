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
use Zend\SignalSlot\Signals,
    Zend\SignalSlot\Slot;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class SignalsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->signals = new Signals;
    }

    public function testConnectShouldReturnSlot()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof Slot);
    }

    public function testConnectShouldAddSlotToTopic()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getSlots('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testConnectShouldAddTopicIfItDoesNotExist()
    {
        $topics = $this->signals->getTopics();
        $this->assertTrue(empty($topics), var_export($topics, 1));
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $topics = $this->signals->getTopics();
        $this->assertFalse(empty($topics));
        $this->assertContains('test', $topics);
    }

    public function testDetachShouldRemoveSlotFromTopic()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getSlots('test');
        $this->assertContains($handle, $handles);
        $this->signals->detach($handle);
        $handles = $this->signals->getSlots('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfTopicDoesNotExist()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearSlots('test');
        $this->assertFalse($this->signals->detach($handle));
    }

    public function testDetachShouldReturnFalseIfSlotDoesNotExist()
    {
        $handle1 = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearSlots('test');
        $handle2 = $this->signals->connect('test', $this, 'handleTestTopic');
        $this->assertFalse($this->signals->detach($handle1));
    }

    public function testRetrievingConnectdSlotsShouldReturnEmptyArrayWhenTopicDoesNotExist()
    {
        $handles = $this->signals->getSlots('test');
        $this->assertTrue(empty($handles));
    }

    public function testNotifyShouldNotifyConnectdSlots()
    {
        $handle = $this->signals->connect('test', $this, 'handleTestTopic');
        $this->signals->notify('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testNotifyShouldReturnTheReturnValueOfTheLastInvokedConnectr()
    {
        $this->signals->connect('string.transform', 'trim');
        $this->signals->connect('string.transform', 'str_rot13');
        $value = $this->signals->notify('string.transform', ' foo ');
        $this->assertEquals(\str_rot13(' foo '), $value);
    }

    public function testNotifyUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->signals->connect('foo.bar', 'strpos');
        $this->signals->connect('foo.bar', 'strstr');
        $value = $this->signals->notifyUntil(
            array($this, 'evaluateStringCallback'), 
            'foo.bar',
            'foo', 'f'
        );
        $this->assertSame(0, $value);
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }
}
