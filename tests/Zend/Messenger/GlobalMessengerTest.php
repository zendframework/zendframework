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
use Zend\SignalSlot\GlobalSignals as SignalSlot,
    Zend\SignalSlot\Handler;

class GlobalSignalsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->clearAllTopics();
    }

    public function tearDown()
    {
        $this->clearAllTopics();
    }

    public function clearAllTopics()
    {
        SignalSlot::setInstance();
    }

    public function testAttachShouldReturnHandler()
    {
        $handle = SignalSlot::attach('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof Handler);
    }

    public function testAttachShouldAddHandlerToTopic()
    {
        $handle = SignalSlot::attach('test', $this, __METHOD__);
        $handles = SignalSlot::getHandlers('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testAttachShouldAddTopicIfItDoesNotExist()
    {
        $topics = SignalSlot::getTopics();
        $this->assertTrue(empty($topics), var_export($topics, 1));
        $handle = SignalSlot::attach('test', $this, __METHOD__);
        $topics = SignalSlot::getTopics();
        $this->assertFalse(empty($topics));
        $this->assertContains('test', $topics);
    }

    public function testDetachShouldRemoveHandlerFromTopic()
    {
        $handle = SignalSlot::attach('test', $this, __METHOD__);
        $handles = SignalSlot::getHandlers('test');
        $this->assertContains($handle, $handles);
        SignalSlot::detach($handle);
        $handles = SignalSlot::getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfTopicDoesNotExist()
    {
        $handle = SignalSlot::attach('test', $this, __METHOD__);
        SignalSlot::clearHandlers('test');
        $this->assertFalse(SignalSlot::detach($handle));
    }

    public function testDetachShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = SignalSlot::attach('test', $this, __METHOD__);
        SignalSlot::clearHandlers('test');
        $handle2 = SignalSlot::attach('test', $this, 'handleTestTopic');
        $this->assertFalse(SignalSlot::detach($handle1));
    }

    public function testRetrievingAttachedHandlersShouldReturnEmptyArrayWhenTopicDoesNotExist()
    {
        $handles = SignalSlot::getHandlers('test');
        $this->assertTrue(empty($handles));
    }

    public function testNotifyShouldNotifyAttachedHandlers()
    {
        $handle = SignalSlot::attach('test', $this, 'handleTestTopic');
        SignalSlot::notify('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testNotifyUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        SignalSlot::attach('foo.bar', 'strpos');
        SignalSlot::attach('foo.bar', 'strstr');
        $value = SignalSlot::notifyUntil(
            function ($value) { return (!$value); },
            'foo.bar',
            'foo', 'f'
        );
        $this->assertSame(0, $value);
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }
}
