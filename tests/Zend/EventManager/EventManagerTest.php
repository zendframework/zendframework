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
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\EventManager;
use Zend\EventManager\EventManager,
    Zend\EventManager\ResponseCollection,
    Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->events = new EventManager;
    }

    public function testConnectShouldReturnCallbackHandler()
    {
        $handle = $this->events->connect('test', array($this, __METHOD__));
        $this->assertTrue($handle instanceof CallbackHandler);
    }

    public function testConnectShouldAddHandlerToEvent()
    {
        $handle = $this->events->connect('test', array($this, __METHOD__));
        $handles = $this->events->getHandlers('test');
        $this->assertEquals(1, count($handles));
        $this->assertContains($handle, $handles);
    }

    public function testConnectShouldAddEventIfItDoesNotExist()
    {
        $events = $this->events->getEvents();
        $this->assertTrue(empty($events), var_export($events, 1));
        $handle  = $this->events->connect('test', array($this, __METHOD__));
        $events = $this->events->getEvents();
        $this->assertFalse(empty($events));
        $this->assertContains('test', $events);
    }

    public function testDetachShouldRemoveHandlerFromEvent()
    {
        $handle = $this->events->connect('test', array($this, __METHOD__));
        $handles = $this->events->getHandlers('test');
        $this->assertContains($handle, $handles);
        $this->events->detach($handle);
        $handles = $this->events->getHandlers('test');
        $this->assertNotContains($handle, $handles);
    }

    public function testDetachShouldReturnFalseIfEventDoesNotExist()
    {
        $handle = $this->events->connect('test', array($this, __METHOD__));
        $this->events->clearHandlers('test');
        $this->assertFalse($this->events->detach($handle));
    }

    public function testDetachShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = $this->events->connect('test', array($this, __METHOD__));
        $this->events->clearHandlers('test');
        $handle2 = $this->events->connect('test', array($this, 'handleTestEvent'));
        $this->assertFalse($this->events->detach($handle1));
    }

    public function testRetrievingConnectedHandlersShouldReturnEmptyArrayWhenEventDoesNotExist()
    {
        $handles = $this->events->getHandlers('test');
        $this->assertEquals(0, count($handles));
    }

    public function testEmitShouldEmitConnectedHandlers()
    {
        $handle = $this->events->connect('test', array($this, 'handleTestEvent'));
        $this->events->emit('test', $this, array('message' => 'test message'));
        $this->assertEquals('test message', $this->message);
    }

    public function testEmitShouldReturnAllHandlerReturnValues()
    {
        $this->events->connect('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return trim($string);
        });
        $this->events->connect('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return str_rot13($string);
        });
        $responses = $this->events->emit('string.transform', $this, array('string' => ' foo '));
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertEquals(2, $responses->count());
        $this->assertEquals('foo', $responses->first());
        $this->assertEquals(\str_rot13(' foo '), $responses->last());
    }

    public function testEmitUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->events->connect('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strpos($string, $search);
        });
        $this->events->connect('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strstr($string, $search);
        });
        $responses = $this->events->emitUntil(
            'foo.bar',
            $this,
            array('string' => 'foo', 'search' => 'f'),
            array($this, 'evaluateStringCallback')
        );
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertSame(0, $responses->last());
    }

    public function testEmitResponseCollectionContains()
    {
        $this->events->connect('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return trim($string);
        });
        $this->events->connect('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return str_rot13($string);
        });
        $responses = $this->events->emit('string.transform', $this, array('string' => ' foo '));
        $this->assertTrue($responses->contains('foo'));
        $this->assertTrue($responses->contains(\str_rot13(' foo ')));
        $this->assertFalse($responses->contains(' foo '));
    }

    public function handleTestEvent($e)
    {
        $message = $e->getParam('message', '__NOT_FOUND__');
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }

    public function testEmitUntilShouldMarkResponseCollectionStoppedWhenConditionMet()
    {
        $this->events->connect('foo.bar', function () { return 'bogus'; }, 4);
        $this->events->connect('foo.bar', function () { return 'nada'; }, 3);
        $this->events->connect('foo.bar', function () { return 'found'; }, 2);
        $this->events->connect('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->events->emitUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $result = $responses->last();
        $this->assertEquals('found', $result);
        $this->assertFalse($responses->contains('zero'));
    }

    public function testEmitUntilShouldMarkResponseCollectionStoppedWhenConditionMetByLastHandler()
    {
        $this->events->connect('foo.bar', function () { return 'bogus'; });
        $this->events->connect('foo.bar', function () { return 'nada'; });
        $this->events->connect('foo.bar', function () { return 'zero'; });
        $this->events->connect('foo.bar', function () { return 'found'; });
        $responses = $this->events->emitUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('found', $responses->last());
    }

    public function testResponseCollectionIsNotStoppedWhenNoCallbackMatchedByEmitUntil()
    {
        $this->events->connect('foo.bar', function () { return 'bogus'; }, 4);
        $this->events->connect('foo.bar', function () { return 'nada'; }, 3);
        $this->events->connect('foo.bar', function () { return 'found'; }, 2);
        $this->events->connect('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->events->emitUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'never found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertFalse($responses->stopped());
        $this->assertEquals('zero', $responses->last());
    }

    public function testConnectAllowsPassingAHandlerAggregateInstance()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->connect($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testPassingHandlerAggregateInstanceToConnectReturnsHandlerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $test      = $this->events->connect($aggregate);
        $this->assertSame($aggregate, $test);
    }

    public function testConnectAllowsPassingAHandlerAggregateClassName()
    {
        $this->events->connect('ZendTest\EventManager\TestAsset\MockAggregate');
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testPassingHandlerAggregateClassNameToConnectReturnsHandlerAggregateInstance()
    {
        $test = $this->events->connect('ZendTest\EventManager\TestAsset\MockAggregate');
        $this->assertInstanceOf('ZendTest\EventManager\TestAsset\MockAggregate', $test);
    }

    public function testCanDetachHandlerAggregates()
    {
        // setup some other event handlers, to ensure appropriate items are detached
        $handlerFooBar1 = $this->events->connect('foo.bar', function(){ return true; });
        $handlerFooBar2 = $this->events->connect('foo.bar', function(){ return true; });
        $handlerFooBaz1 = $this->events->connect('foo.baz', function(){ return true; });
        $handlerOther   = $this->events->connect('other', function(){ return true; });

        $aggregate = new TestAsset\MockAggregate();
        $this->events->connect($aggregate);
        $this->events->detach($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz', 'other') as $event) {
            $this->assertContains($event, $events);
        }

        $handlers = $this->events->getHandlers('foo.bar');
        $this->assertEquals(2, count($handlers));
        $this->assertContains($handlerFooBar1, $handlers);
        $this->assertContains($handlerFooBar2, $handlers);

        $handlers = $this->events->getHandlers('foo.baz');
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handlerFooBaz1, $handlers);

        $handlers = $this->events->getHandlers('other');
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handlerOther, $handlers);
    }

    public function testCallingEventsStopPropagationMethodHaltsEventEmission()
    {
        $this->events->connect('foo.bar', function ($e) { return 'bogus'; }, 4);
        $this->events->connect('foo.bar', function ($e) { $e->stopPropagation(true); return 'nada'; }, 3);
        $this->events->connect('foo.bar', function ($e) { return 'found'; }, 2);
        $this->events->connect('foo.bar', function ($e) { return 'zero'; }, 1);
        $responses = $this->events->emit('foo.bar', $this, array());
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('nada', $responses->last());
        $this->assertTrue($responses->contains('bogus'));
        $this->assertFalse($responses->contains('found'));
        $this->assertFalse($responses->contains('zero'));
    }

    public function testCanAlterParametersWithinAEvent()
    {
        $this->events->connect('foo.bar', function ($e) { $e->setParam('foo', 'bar'); });
        $this->events->connect('foo.bar', function ($e) { $e->setParam('bar', 'baz'); });
        $this->events->connect('foo.bar', function ($e) {
            $foo = $e->getParam('foo', '__NO_FOO__');
            $bar = $e->getParam('bar', '__NO_BAR__');
            return $foo . ":" . $bar;
        });
        $responses = $this->events->emit('foo.bar', $this, array());
        $this->assertEquals('bar:baz', $responses->last());
    }

    public function testParametersArePassedToEventByReference()
    {
        $params = array( 'foo' => 'bar', 'bar' => 'baz');
        $args   = $this->events->prepareArgs($params);
        $this->events->connect('foo.bar', function ($e) { $e->setParam('foo', 'FOO'); });
        $this->events->connect('foo.bar', function ($e) { $e->setParam('bar', 'BAR'); });
        $responses = $this->events->emit('foo.bar', $this, $args);
        $this->assertEquals('FOO', $args['foo']);
        $this->assertEquals('BAR', $args['bar']);
    }
}
