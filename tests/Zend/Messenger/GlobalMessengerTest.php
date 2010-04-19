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

namespace ZendTest\Messenger;
use Zend\Messenger\GlobalMessenger as Messenger,
    Zend\Messenger\Handler;

class GlobalMessengerTest extends \PHPUnit_Framework_TestCase
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
        Messenger::setInstance();
    }

    public function testAttachShouldReturnHandler()
    {
        $handle = Messenger::attach('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof Handler);
    }

    public function testAttachShouldAddHandlerToTopic()
    {
        $handle = Messenger::attach('test', $this, __METHOD__);
        $handles = Messenger::getHandlers('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testAttachShouldAddTopicIfItDoesNotExist()
    {
        $topics = Messenger::getTopics();
        $this->assertTrue(empty($topics), var_export($topics, 1));
        $handle = Messenger::attach('test', $this, __METHOD__);
        $topics = Messenger::getTopics();
        $this->assertFalse(empty($topics));
        $this->assertContains('test', $topics);
    }

    public function testDetachShouldRemoveHandlerFromTopic()
    {
        $handle = Messenger::attach('test', $this, __METHOD__);
        $handles = Messenger::getHandlers('test');
        $this->assertContains($handle, $handles);
        Messenger::detach($handle);
        $handles = Messenger::getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfTopicDoesNotExist()
    {
        $handle = Messenger::attach('test', $this, __METHOD__);
        Messenger::clearHandlers('test');
        $this->assertFalse(Messenger::detach($handle));
    }

    public function testDetachShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = Messenger::attach('test', $this, __METHOD__);
        Messenger::clearHandlers('test');
        $handle2 = Messenger::attach('test', $this, 'handleTestTopic');
        $this->assertFalse(Messenger::detach($handle1));
    }

    public function testRetrievingAttachedHandlersShouldReturnEmptyArrayWhenTopicDoesNotExist()
    {
        $handles = Messenger::getHandlers('test');
        $this->assertTrue(empty($handles));
    }

    public function testNotifyShouldNotifyAttachedHandlers()
    {
        $handle = Messenger::attach('test', $this, 'handleTestTopic');
        Messenger::notify('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testNotifyUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        Messenger::attach('foo.bar', 'strpos');
        Messenger::attach('foo.bar', 'strstr');
        $value = Messenger::notifyUntil(
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
