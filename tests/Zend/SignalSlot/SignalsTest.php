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
use Zend\SignalSlot\Signals,
    Zend\SignalSlot\ResponseCollection,
    Zend\Stdlib\SignalHandler;

/**
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @group      Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

    public function testConnectShouldReturnSignalHandler()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $this->assertTrue($handle instanceof SignalHandler);
    }

    public function testConnectShouldAddHandlerToSignal()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getHandlers('test');
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

    public function testDetachShouldRemoveHandlerFromSignal()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $handles = $this->signals->getHandlers('test');
        $this->assertContains($handle, $handles);
        $this->signals->detach($handle);
        $handles = $this->signals->getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfSignalDoesNotExist()
    {
        $handle = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearHandlers('test');
        $this->assertFalse($this->signals->detach($handle));
    }

    public function testDetachShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = $this->signals->connect('test', $this, __METHOD__);
        $this->signals->clearHandlers('test');
        $handle2 = $this->signals->connect('test', $this, 'handleTestSignal');
        $this->assertFalse($this->signals->detach($handle1));
    }

    public function testRetrievingConnectedHandlersShouldReturnEmptyArrayWhenSignalDoesNotExist()
    {
        $handles = $this->signals->getHandlers('test');
        $this->assertTrue(empty($handles));
    }

    public function testEmitShouldEmitConnectedHandlers()
    {
        $handle = $this->signals->connect('test', $this, 'handleTestSignal');
        $this->signals->emit('test', 'test message');
        $this->assertEquals('test message', $this->message);
    }

    public function testEmitShouldReturnAllHandlerReturnValues()
    {
        $this->signals->connect('string.transform', 'trim');
        $this->signals->connect('string.transform', 'str_rot13');
        $responses = $this->signals->emit('string.transform', ' foo ');
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertEquals(2, $responses->count());
        $this->assertEquals('foo', $responses->first());
        $this->assertEquals(\str_rot13(' foo '), $responses->last());
    }

    public function testEmitUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->signals->connect('foo.bar', 'strpos');
        $this->signals->connect('foo.bar', 'strstr');
        $responses = $this->signals->emitUntil(
            array($this, 'evaluateStringCallback'), 
            'foo.bar',
            'foo', 'f'
        );
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertSame(0, $responses->last());
    }

    public function testEmitResponseCollectionContains()
    {
        $this->signals->connect('string.transform', 'trim');
        $this->signals->connect('string.transform', 'str_rot13');
        $responses = $this->signals->emit('string.transform', ' foo ');
        $this->assertTrue($responses->contains('foo'));
        $this->assertTrue($responses->contains(\str_rot13(' foo ')));
        $this->assertFalse($responses->contains(' foo '));
    }

    public function handleTestSignal($message)
    {
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }

    public function testEmitUntilShouldMarkResponseCollectionStoppedWhenConditionMet()
    {
        $this->signals->connect('foo.bar', function () { return 'bogus'; });
        $this->signals->connect('foo.bar', function () { return 'nada'; });
        $this->signals->connect('foo.bar', function () { return 'found'; });
        $this->signals->connect('foo.bar', function () { return 'zero'; });
        $responses = $this->signals->emitUntil(function ($result) {
            return ($result === 'found');
        }, 'foo.bar');
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $result = $responses->last();
        $this->assertEquals('found', $result);
        $this->assertFalse($responses->contains('zero'));
    }

    public function testEmitUntilShouldMarkResponseCollectionStoppedWhenConditionMetByLastHandler()
    {
        $this->signals->connect('foo.bar', function () { return 'bogus'; });
        $this->signals->connect('foo.bar', function () { return 'nada'; });
        $this->signals->connect('foo.bar', function () { return 'zero'; });
        $this->signals->connect('foo.bar', function () { return 'found'; });
        $responses = $this->signals->emitUntil(function ($result) {
            return ($result === 'found');
        }, 'foo.bar');
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('found', $responses->last());
    }

    public function testResponseCollectionIsNotStoppedWhenNoCallbackMatchedByEmitUntil()
    {
        $this->signals->connect('foo.bar', function () { return 'bogus'; });
        $this->signals->connect('foo.bar', function () { return 'nada'; });
        $this->signals->connect('foo.bar', function () { return 'found'; });
        $this->signals->connect('foo.bar', function () { return 'zero'; });
        $responses = $this->signals->emitUntil(function ($result) {
            return ($result === 'never found');
        }, 'foo.bar');
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertFalse($responses->stopped());
        $this->assertEquals('zero', $responses->last());
    }

    public function testConnectAllowsPassingASignalAggregateInstance()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->signals->connect($aggregate);
        $signals = $this->signals->getSignals();
        foreach (array('foo.bar', 'foo.baz') as $signal) {
            $this->assertContains($signal, $signals);
        }
    }

    public function testPassingSignalAggregateInstanceToConnectReturnsSignalAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $test      = $this->signals->connect($aggregate);
        $this->assertSame($aggregate, $test);
    }

    public function testConnectAllowsPassingASignalAggregateClassName()
    {
        $this->signals->connect('ZendTest\SignalSlot\TestAsset\MockAggregate');
        $signals = $this->signals->getSignals();
        foreach (array('foo.bar', 'foo.baz') as $signal) {
            $this->assertContains($signal, $signals);
        }
    }

    public function testPassingSignalAggregateClassNameToConnectReturnsSignalAggregateInstance()
    {
        $test = $this->signals->connect('ZendTest\SignalSlot\TestAsset\MockAggregate');
        $this->assertInstanceOf('ZendTest\SignalSlot\TestAsset\MockAggregate', $test);
    }

    public function testCanDetachSignalAggregates()
    {
        // setup some other signal handlers, to ensure appropriate items are detached
        $handlerFooBar1 = $this->signals->connect('foo.bar', function(){ return true; });
        $handlerFooBar2 = $this->signals->connect('foo.bar', function(){ return true; });
        $handlerFooBaz1 = $this->signals->connect('foo.baz', function(){ return true; });
        $handlerOther   = $this->signals->connect('other', function(){ return true; });

        $aggregate = new TestAsset\MockAggregate();
        $this->signals->connect($aggregate);
        $this->signals->detach($aggregate);
        $signals = $this->signals->getSignals();
        foreach (array('foo.bar', 'foo.baz', 'other') as $signal) {
            $this->assertContains($signal, $signals);
        }

        $handlers = $this->signals->getHandlers('foo.bar');
        $this->assertEquals(2, count($handlers));
        $this->assertContains($handlerFooBar1, $handlers);
        $this->assertContains($handlerFooBar2, $handlers);

        $handlers = $this->signals->getHandlers('foo.baz');
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handlerFooBaz1, $handlers);

        $handlers = $this->signals->getHandlers('other');
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handlerOther, $handlers);
    }
}
