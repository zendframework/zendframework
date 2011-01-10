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
use Zend\SignalSlot\StaticSignalSlot as SignalSlot,
    Zend\SignalSlot\ResponseCollection,
    Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @group      Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticSignalSlotTest extends \PHPUnit_Framework_TestCase
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

    public function testConnectShouldReturnCallbackHandler()
    {
        $handle = SignalSlot::connect('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof CallbackHandler);
    }

    public function testConnectShouldAddCallbackHandlerToSignal()
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

    public function testDetachShouldRemoveCallbackHandlerFromSignal()
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

    public function testDetachShouldReturnFalseIfCallbackHandlerDoesNotExist()
    {
        $handle1 = SignalSlot::connect('test', $this, __METHOD__);
        SignalSlot::clearHandlers('test');
        $handle2 = SignalSlot::connect('test', $this, 'handleTestTopic');
        $this->assertFalse(SignalSlot::detach($handle1));
    }

    public function testRetrievingAttachedCallbackHandlersShouldReturnEmptyArrayWhenSignalDoesNotExist()
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
        $responses = SignalSlot::emitUntil(
            function ($value) { return (!$value); },
            'foo.bar',
            'foo', 'f'
        );
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertSame(0, $responses->last());
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }
}
