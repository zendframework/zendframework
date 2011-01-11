<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\SignalSlot;
use Zend\SignalSlot\StaticSignalSlot,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @group      Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticSignalSlotTest extends TestCase
{
    public function setUp()
    {
        StaticSignalSlot::resetInstance();
    }

    public function tearDown()
    {
        StaticSignalSlot::resetInstance();
    }

    public function testOperatesAsASingleton()
    {
        $expected = StaticSignalSlot::getInstance();
        $test     = StaticSignalSlot::getInstance();
        $this->assertSame($expected, $test);
    }

    public function testCanResetInstance()
    {
        $original = StaticSignalSlot::getInstance();
        StaticSignalSlot::resetInstance();
        $test = StaticSignalSlot::getInstance();
        $this->assertNotSame($original, $test);
    }

    public function testSingletonInstanceIsInstanceOfClass()
    {
        $this->assertInstanceOf('Zend\SignalSlot\StaticSignalSlot', StaticSignalSlot::getInstance());
    }

    public function testCanConnectCallbackToSignal()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $signals->getSignals('foo'));
        $expected = array($this, __FUNCTION__);
        $found    = false;
        $slots    = $signals->getSlots('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $slots);
        $this->assertTrue(0 < count($slots), 'Empty slots!');
        foreach ($slots as $slot) {
            if ($expected === $slot->getCallback()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Did not find slot!');
    }

    public function testCanConnectSameSignalToMultipleResourcesAtOnce()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect(array('foo', 'test'), 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $signals->getSignals('foo'));
        $this->assertContains('bar', $signals->getSignals('test'));
        $expected = array($this, __FUNCTION__);
        foreach (array('foo', 'test') as $id) {
            $found    = false;
            $slots    = $signals->getSlots($id, 'bar');
            $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $slots);
            $this->assertTrue(0 < count($slots), 'Empty slots!');
            foreach ($slots as $slot) {
                if ($expected === $slot->getCallback()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Did not find slot!');
        }
    }

    public function testCanDetachSlotFromResource()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        foreach ($signals->getSlots('foo', 'bar') as $slot) {
            // only one; retrieving it so we can detach
        }
        $signals->detach('foo', $slot);
        $slots = $signals->getSlots('foo', 'bar');
        $this->assertEquals(0, count($slots));
    }

    public function testCanGetSignalsByResource()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        $this->assertEquals(array('bar'), $signals->getSignals('foo'));
    }

    public function testCanGetSlotsByResourceAndSignal()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        $slots = $signals->getSlots('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $slots);
        $this->assertEquals(1, count($slots));
    }

    public function testCanClearSlotsByResource()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        $signals->connect('foo', 'baz', array($this, __FUNCTION__));
        $signals->clearSlots('foo');
        $this->assertFalse($signals->getSlots('foo', 'bar'));
        $this->assertFalse($signals->getSlots('foo', 'baz'));
    }

    public function testCanClearSlotsByResourceAndSignal()
    {
        $signals = StaticSignalSlot::getInstance();
        $signals->connect('foo', 'bar', array($this, __FUNCTION__));
        $signals->connect('foo', 'baz', array($this, __FUNCTION__));
        $signals->connect('foo', 'bat', array($this, __FUNCTION__));
        $signals->clearSlots('foo', 'baz');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $signals->getSlots('foo', 'baz'));
        $this->assertEquals(0, count($signals->getSlots('foo', 'baz')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $signals->getSlots('foo', 'bar'));
        $this->assertEquals(1, count($signals->getSlots('foo', 'bar')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $signals->getSlots('foo', 'bat'));
        $this->assertEquals(1, count($signals->getSlots('foo', 'bat')));
    }
}
