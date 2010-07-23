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
    Zend\Stdlib\SignalHandler;

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

    public function testConnectShouldReturnSignalHandler()
    {
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof SignalHandler);
    }

    public function testConnectShouldAddSignalHandlerToSignal()
    {
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        $handles = SignalSlot::getHandlers('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testConnectShouldAddSignalIfItDoesNotExist()
    {
        $signals = SignalSlot::getSignals();
        $this->assertTrue(empty($signals), var_export($signals, 1));
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        $signals = SignalSlot::getSignals();
        $this->assertFalse(empty($signals));
        $this->assertContains('test', $signals);
    }

    public function testDetachShouldRemoveSignalHandlerFromSignal()
    {
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        $handles = SignalSlot::getHandlers('test');
        $this->assertContains($handle, $handles);
        SignalSlot::detach($handle);
        $handles = SignalSlot::getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfSignalDoesNotExist()
    {
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        SignalSlot::clearHandlers('test');
        $this->assertFalse(SignalSlot::detach($handle));
    }

    public function testDetachShouldReturnFalseIfSignalHandlerDoesNotExist()
    {
        $handle1 = SignalSlot::connect('test', $this, __METHOD__);
        SignalSlot::clearHandlers('test');
        $handle2 = SignalSlot::connect('test', $this, 'handleTestTopic');
        $this->assertFalse(SignalSlot::detach($handle1));
    }

    public function testRetrievingAttachedSignalHandlersShouldReturnEmptyArrayWhenSignalDoesNotExist()
    {
        $handles = SignalSlot::getHandlers('test');
        $this->assertTrue(empty($handles));
    }

    public function testEmitShouldEmitAttachedHandlers()
    {
        $handle = SignalSlot::connect('test', $this, 'handleTestTopic');
        SignalSlot::emit('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testEmitUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        SignalSlot::connect('foo.bar', 'strpos');
        SignalSlot::connect('foo.bar', 'strstr');
        $value = SignalSlot::emitUntil(
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
