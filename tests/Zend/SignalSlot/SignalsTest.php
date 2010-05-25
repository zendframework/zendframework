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

    public function testConnectShouldAddSlotToSignal()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getSlots('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testConnectShouldAddSignalIfItDoesNotExist()
    {
        $signals = $this->signals->getSignals();
        $this->assertTrue(empty($signals), var_export($signals, 1));
        $handle  = $this->signals->connect('test', $this, __METHOD__);
        $signals = $this->signals->getSignals();
        $this->assertFalse(empty($signals));
        $this->assertContains('test', $signals);
    }

    public function testDetachShouldRemoveSlotFromSignal()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getSlots('test');
        $this->assertContains($handle, $handles);
        $this->signals->detach($handle);
        $handles = $this->signals->getSlots('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfSignalDoesNotExist()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearSlots('test');
        $this->assertFalse($this->signals->detach($handle));
    }

    public function testDetachShouldReturnFalseIfSlotDoesNotExist()
    {
        $handle1 = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearSlots('test');
        $handle2 = $this->signals->connect('test', $this, 'handleTestSignal');
        $this->assertFalse($this->signals->detach($handle1));
    }

    public function testRetrievingConnectdSlotsShouldReturnEmptyArrayWhenSignalDoesNotExist()
    {
        $handles = $this->signals->getSlots('test');
        $this->assertTrue(empty($handles));
    }

    public function testEmitShouldEmitConnectedSlots()
    {
        $handle = $this->signals->connect('test', $this, 'handleTestSignal');
        $this->signals->emit('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testEmitShouldReturnTheReturnValueOfTheLastInvokedConnectr()
    {
        $this->signals->connect('string.transform', 'trim');
        $this->signals->connect('string.transform', 'str_rot13');
        $value = $this->signals->emit('string.transform', ' foo ');
        $this->assertEquals(\str_rot13(' foo '), $value);
    }

    public function testEmitUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->signals->connect('foo.bar', 'strpos');
        $this->signals->connect('foo.bar', 'strstr');
        $value = $this->signals->emitUntil(
            array($this, 'evaluateStringCallback'), 
            'foo.bar',
            'foo', 'f'
        );
        $this->assertSame(0, $value);
    }

    public function handleTestSignal($message)
    {
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }
}
