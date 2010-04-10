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
use Zend\Messenger\Messenger,
    Zend\Messenger\Handler;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class MessengerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->messenger = new Messenger;
    }

    public function testAttachShouldReturnHandler()
    {
        $handle = $this->messenger->attach('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof Handler);
    }

    public function testAttachShouldAddHandlerToTopic()
    {
        $handle = $this->messenger->attach('test', $this, __METHOD__);
        $handles = $this->messenger->getHandlers('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testAttachShouldAddTopicIfItDoesNotExist()
    {
        $topics = $this->messenger->getTopics();
        $this->assertTrue(empty($topics), var_export($topics, 1));
        $handle = $this->messenger->attach('test', $this, __METHOD__);
        $topics = $this->messenger->getTopics();
        $this->assertFalse(empty($topics));
        $this->assertContains('test', $topics);
    }

    public function testDetachShouldRemoveHandlerFromTopic()
    {
        $handle = $this->messenger->attach('test', $this, __METHOD__);
        $handles = $this->messenger->getHandlers('test');
        $this->assertContains($handle, $handles);
        $this->messenger->detach($handle);
        $handles = $this->messenger->getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfTopicDoesNotExist()
    {
        $handle = $this->messenger->attach('test', $this, __METHOD__);
        $this->messenger->clearHandlers('test');
        $this->assertFalse($this->messenger->detach($handle));
    }

    public function testDetachShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = $this->messenger->attach('test', $this, __METHOD__);
        $this->messenger->clearHandlers('test');
        $handle2 = $this->messenger->attach('test', $this, 'handleTestTopic');
        $this->assertFalse($this->messenger->detach($handle1));
    }

    public function testRetrievingAttachdHandlersShouldReturnEmptyArrayWhenTopicDoesNotExist()
    {
        $handles = $this->messenger->getHandlers('test');
        $this->assertTrue(empty($handles));
    }

    public function testNotifyShouldNotifyAttachdHandlers()
    {
        $handle = $this->messenger->attach('test', $this, 'handleTestTopic');
        $this->messenger->notify('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testNotifyShouldReturnTheReturnValueOfTheLastInvokedAttachr()
    {
        $this->messenger->attach('string.transform', 'trim');
        $this->messenger->attach('string.transform', 'str_rot13');
        $value = $this->messenger->notify('string.transform', ' foo ');
        $this->assertEquals(\str_rot13(' foo '), $value);
    }

    public function testNotifyUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->messenger->attach('foo.bar', 'strpos');
        $this->messenger->attach('foo.bar', 'strstr');
        $value = $this->messenger->notifyUntil(
            array($this, 'evaluateStringCallback'), 
            'foo.bar',
            'foo', 'f'
        );
        $this->assertSame(0, $value);
    }

    public function testFilterShouldPassReturnValueOfEachAttachrToNextAttachr()
    {
        $this->messenger->attach('string.transform', 'trim');
        $this->messenger->attach('string.transform', 'str_rot13');
        $value = $this->messenger->filter('string.transform', ' foo ');
        $this->assertEquals(\str_rot13('foo'), $value);
    }

    public function testFilterShouldAllowMultipleArgumentsButFilterOnlyFirst()
    {
        $this->messenger->attach('filter.test', $this, 'filterTestCallback1');
        $this->messenger->attach('filter.test', $this, 'filterTestCallback2');
        $obj = (object) array('foo' => 'bar', 'bar' => 'baz');
        $value = $this->messenger->filter('filter.test', '', $obj);
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
