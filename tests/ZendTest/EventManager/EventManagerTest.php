<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\EventManager;

use ArrayIterator;
use stdClass;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\StaticEventManager;
use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        StaticEventManager::resetInstance();

        if (isset($this->message)) {
            unset($this->message);
        }
        $this->events = new EventManager;
        StaticEventManager::resetInstance();
    }

    public function tearDown()
    {
        StaticEventManager::resetInstance();
    }

    public function testAttachShouldReturnCallbackHandler()
    {
        $listener = $this->events->attach('test', array($this, __METHOD__));
        $this->assertTrue($listener instanceof CallbackHandler);
    }

    public function testAttachShouldAddListenerToEvent()
    {
        $listener  = $this->events->attach('test', array($this, __METHOD__));
        $listeners = $this->events->getListeners('test');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listener, $listeners);
    }

    public function testAttachShouldAddEventIfItDoesNotExist()
    {
        $events = $this->events->getEvents();
        $this->assertTrue(empty($events), var_export($events, 1));
        $listener = $this->events->attach('test', array($this, __METHOD__));
        $events = $this->events->getEvents();
        $this->assertFalse(empty($events));
        $this->assertContains('test', $events);
    }

    public function testAllowsPassingArrayOfEventNamesWhenAttaching()
    {
        $callback = function ($e) {
            return $e->getName();
        };
        $this->events->attach(array('foo', 'bar'), $callback);

        foreach (array('foo', 'bar') as $event) {
            $listeners = $this->events->getListeners($event);
            $this->assertTrue(count($listeners) > 0);
            foreach ($listeners as $listener) {
                $this->assertSame($callback, $listener->getCallback());
            }
        }
    }

    public function testPassingArrayOfEventNamesWhenAttachingReturnsArrayOfCallbackHandlers()
    {
        $callback = function ($e) {
            return $e->getName();
        };
        $listeners = $this->events->attach(array('foo', 'bar'), $callback);

        $this->assertInternalType('array', $listeners);

        foreach ($listeners as $listener) {
            $this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener);
            $this->assertSame($callback, $listener->getCallback());
        }
    }

    public function testDetachShouldRemoveListenerFromEvent()
    {
        $listener  = $this->events->attach('test', array($this, __METHOD__));
        $listeners = $this->events->getListeners('test');
        $this->assertContains($listener, $listeners);
        $this->events->detach($listener);
        $listeners = $this->events->getListeners('test');
        $this->assertNotContains($listener, $listeners);
    }

    public function testDetachShouldReturnFalseIfEventDoesNotExist()
    {
        $listener = $this->events->attach('test', array($this, __METHOD__));
        $this->events->clearListeners('test');
        $this->assertFalse($this->events->detach($listener));
    }

    public function testDetachShouldReturnFalseIfListenerDoesNotExist()
    {
        $listener1 = $this->events->attach('test', array($this, __METHOD__));
        $this->events->clearListeners('test');
        $listener2 = $this->events->attach('test', array($this, 'handleTestEvent'));
        $this->assertFalse($this->events->detach($listener1));
    }

    public function testRetrievingAttachedListenersShouldReturnEmptyArrayWhenEventDoesNotExist()
    {
        $listeners = $this->events->getListeners('test');
        $this->assertEquals(0, count($listeners));
    }

    public function testTriggerShouldTriggerAttachedListeners()
    {
        $listener = $this->events->attach('test', array($this, 'handleTestEvent'));
        $this->events->trigger('test', $this, array('message' => 'test message'));
        $this->assertEquals('test message', $this->message);
    }

    public function testTriggerShouldReturnAllListenerReturnValues()
    {
        $this->events->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return trim($string);
        });
        $this->events->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return str_rot13($string);
        });
        $responses = $this->events->trigger('string.transform', $this, array('string' => ' foo '));
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertEquals(2, $responses->count());
        $this->assertEquals('foo', $responses->first());
        $this->assertEquals(\str_rot13(' foo '), $responses->last());
    }

    public function testTriggerUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->events->attach('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strpos($string, $search);
        });
        $this->events->attach('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strstr($string, $search);
        });
        $responses = $this->events->triggerUntil(
            'foo.bar',
            $this,
            array('string' => 'foo', 'search' => 'f'),
            array($this, 'evaluateStringCallback')
        );
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertSame(0, $responses->last());
    }

    public function testTriggerResponseCollectionContains()
    {
        $this->events->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return trim($string);
        });
        $this->events->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return str_rot13($string);
        });
        $responses = $this->events->trigger('string.transform', $this, array('string' => ' foo '));
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

    public function testTriggerUntilShouldMarkResponseCollectionStoppedWhenConditionMet()
    {
        $this->events->attach('foo.bar', function () { return 'bogus'; }, 4);
        $this->events->attach('foo.bar', function () { return 'nada'; }, 3);
        $this->events->attach('foo.bar', function () { return 'found'; }, 2);
        $this->events->attach('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->events->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $result = $responses->last();
        $this->assertEquals('found', $result);
        $this->assertFalse($responses->contains('zero'));
    }

    public function testTriggerUntilShouldMarkResponseCollectionStoppedWhenConditionMetByLastListener()
    {
        $this->events->attach('foo.bar', function () { return 'bogus'; });
        $this->events->attach('foo.bar', function () { return 'nada'; });
        $this->events->attach('foo.bar', function () { return 'zero'; });
        $this->events->attach('foo.bar', function () { return 'found'; });
        $responses = $this->events->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('found', $responses->last());
    }

    public function testResponseCollectionIsNotStoppedWhenNoCallbackMatchedByTriggerUntil()
    {
        $this->events->attach('foo.bar', function () { return 'bogus'; }, 4);
        $this->events->attach('foo.bar', function () { return 'nada'; }, 3);
        $this->events->attach('foo.bar', function () { return 'found'; }, 2);
        $this->events->attach('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->events->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'never found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertFalse($responses->stopped());
        $this->assertEquals('zero', $responses->last());
    }

    public function testCanAttachListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->attachAggregate($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testCanAttachListenerAggregateViaAttach()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->attach($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testAttachAggregateReturnsAttachOfListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $method    = $this->events->attachAggregate($aggregate);
        $this->assertSame('ZendTest\EventManager\TestAsset\MockAggregate::attach', $method);
    }

    public function testCanDetachListenerAggregates()
    {
        // setup some other event listeners, to ensure appropriate items are detached
        $listenerFooBar1 = $this->events->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBar2 = $this->events->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBaz1 = $this->events->attach('foo.baz', function () {
            return true;
        });
        $listenerOther   = $this->events->attach('other', function () {
            return true;
        });

        $aggregate = new TestAsset\MockAggregate();
        $this->events->attachAggregate($aggregate);
        $this->events->detachAggregate($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz', 'other') as $event) {
            $this->assertContains($event, $events);
        }

        $listeners = $this->events->getListeners('foo.bar');
        $this->assertEquals(2, count($listeners));
        $this->assertContains($listenerFooBar1, $listeners);
        $this->assertContains($listenerFooBar2, $listeners);

        $listeners = $this->events->getListeners('foo.baz');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerFooBaz1, $listeners);

        $listeners = $this->events->getListeners('other');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerOther, $listeners);
    }

    public function testCanDetachListenerAggregatesViaDetach()
    {
        // setup some other event listeners, to ensure appropriate items are detached
        $listenerFooBar1 = $this->events->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBar2 = $this->events->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBaz1 = $this->events->attach('foo.baz', function () {
            return true;
        });
        $listenerOther   = $this->events->attach('other', function () {
            return true;
        });

        $aggregate = new TestAsset\MockAggregate();
        $this->events->attach($aggregate);
        $this->events->detach($aggregate);
        $events = $this->events->getEvents();
        foreach (array('foo.bar', 'foo.baz', 'other') as $event) {
            $this->assertContains($event, $events);
        }

        $listeners = $this->events->getListeners('foo.bar');
        $this->assertEquals(2, count($listeners));
        $this->assertContains($listenerFooBar1, $listeners);
        $this->assertContains($listenerFooBar2, $listeners);

        $listeners = $this->events->getListeners('foo.baz');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerFooBaz1, $listeners);

        $listeners = $this->events->getListeners('other');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerOther, $listeners);
    }

    public function testDetachAggregateReturnsDetachOfListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->attachAggregate($aggregate);
        $method = $this->events->detachAggregate($aggregate);
        $this->assertSame('ZendTest\EventManager\TestAsset\MockAggregate::detach', $method);
    }

    public function testAttachAggregateAcceptsOptionalPriorityValue()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->attachAggregate($aggregate, 1);
        $this->assertEquals(1, $aggregate->priority);
    }

    public function testAttachAggregateAcceptsOptionalPriorityValueViaAttachCallbackArgument()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->events->attach($aggregate, 1);
        $this->assertEquals(1, $aggregate->priority);
    }

    public function testCallingEventsStopPropagationMethodHaltsEventEmission()
    {
        $this->events->attach('foo.bar', function ($e) { return 'bogus'; }, 4);
        $this->events->attach('foo.bar', function ($e) { $e->stopPropagation(true); return 'nada'; }, 3);
        $this->events->attach('foo.bar', function ($e) { return 'found'; }, 2);
        $this->events->attach('foo.bar', function ($e) { return 'zero'; }, 1);
        $responses = $this->events->trigger('foo.bar', $this, array());
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('nada', $responses->last());
        $this->assertTrue($responses->contains('bogus'));
        $this->assertFalse($responses->contains('found'));
        $this->assertFalse($responses->contains('zero'));
    }

    public function testCanAlterParametersWithinAEvent()
    {
        $this->events->attach('foo.bar', function ($e) { $e->setParam('foo', 'bar'); });
        $this->events->attach('foo.bar', function ($e) { $e->setParam('bar', 'baz'); });
        $this->events->attach('foo.bar', function ($e) {
            $foo = $e->getParam('foo', '__NO_FOO__');
            $bar = $e->getParam('bar', '__NO_BAR__');
            return $foo . ":" . $bar;
        });
        $responses = $this->events->trigger('foo.bar', $this, array());
        $this->assertEquals('bar:baz', $responses->last());
    }

    public function testParametersArePassedToEventByReference()
    {
        $params = array( 'foo' => 'bar', 'bar' => 'baz');
        $args   = $this->events->prepareArgs($params);
        $this->events->attach('foo.bar', function ($e) { $e->setParam('foo', 'FOO'); });
        $this->events->attach('foo.bar', function ($e) { $e->setParam('bar', 'BAR'); });
        $responses = $this->events->trigger('foo.bar', $this, $args);
        $this->assertEquals('FOO', $args['foo']);
        $this->assertEquals('BAR', $args['bar']);
    }

    public function testCanPassObjectForEventParameters()
    {
        $params = (object) array( 'foo' => 'bar', 'bar' => 'baz');
        $this->events->attach('foo.bar', function ($e) { $e->setParam('foo', 'FOO'); });
        $this->events->attach('foo.bar', function ($e) { $e->setParam('bar', 'BAR'); });
        $responses = $this->events->trigger('foo.bar', $this, $params);
        $this->assertEquals('FOO', $params->foo);
        $this->assertEquals('BAR', $params->bar);
    }

    public function testCanPassEventObjectAsSoleArgumentToTrigger()
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->trigger($event);
        $this->assertSame($event, $responses->last());
    }

    public function testCanPassEventNameAndEventObjectAsSoleArgumentsToTrigger()
    {
        $event = new Event();
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->trigger(__FUNCTION__, $event);
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
    }

    public function testCanPassEventObjectAsArgvToTrigger()
    {
        $event = new Event();
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->trigger(__FUNCTION__, $this, $event);
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
        $this->assertSame($this, $event->getTarget());
    }

    public function testCanPassEventObjectAndCallbackAsSoleArgumentsToTriggerUntil()
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->triggerUntil($event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
    }

    public function testCanPassEventNameAndEventObjectAndCallbackAsSoleArgumentsToTriggerUntil()
    {
        $event = new Event();
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->triggerUntil(__FUNCTION__, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
    }

    public function testCanPassEventObjectAsArgvToTriggerUntil()
    {
        $event = new Event();
        $event->setParams(array('foo' => 'bar'));
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->events->triggerUntil(__FUNCTION__, $this, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
        $this->assertSame($this, $event->getTarget());
    }

    public function testTriggerCanTakeAnOptionalCallbackArgumentToEmulateTriggerUntil()
    {
        $this->events->attach(__FUNCTION__, function ($e) {
            return $e;
        });

        // Four scenarios:
        // First: normal signature:
        $responses = $this->events->trigger(__FUNCTION__, $this, array(), function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());

        // Second: Event as $argv parameter:
        $event = new Event();
        $responses = $this->events->trigger(__FUNCTION__, $this, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());

        // Third: Event as $target parameter:
        $event = new Event();
        $event->setTarget($this);
        $responses = $this->events->trigger(__FUNCTION__, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());

        // Fourth: Event as $event parameter:
        $event = new Event();
        $event->setTarget($this);
        $event->setName(__FUNCTION__);
        $responses = $this->events->trigger($event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
    }

    public function testWeakRefsAreHonoredWhenTriggering()
    {
        if (!class_exists('WeakRef', false)) {
            $this->markTestSkipped('Requires pecl/weakref');
        }

        $functor = new TestAsset\Functor;
        $this->events->attach('test', $functor);

        unset($functor);

        $result = $this->events->trigger('test', $this, array());
        $message = $result->last();
        $this->assertNull($message);
    }

    public function testDuplicateIdentifiersAreNotRegistered()
    {
        $events = new EventManager(array(__CLASS__, get_class($this)));
        $identifiers = $events->getIdentifiers();
        $this->assertSame(count($identifiers), 1);
        $this->assertSame($identifiers[0], __CLASS__);
        $events->addIdentifiers(__CLASS__);
        $this->assertSame(count($identifiers), 1);
        $this->assertSame($identifiers[0], __CLASS__);
    }

    public function testIdentifierGetterSettersWorkWithStrings()
    {
        $identifier1 = 'foo';
        $identifiers = array($identifier1);
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->setIdentifiers($identifier1));
        $this->assertSame($this->events->getIdentifiers(), $identifiers);
        $identifier2 = 'baz';
        $identifiers = array($identifier1, $identifier2);
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->addIdentifiers($identifier2));
        $this->assertSame($this->events->getIdentifiers(), $identifiers);
    }

    public function testIdentifierGetterSettersWorkWithArrays()
    {
        $identifiers = array('foo', 'bar');
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->setIdentifiers($identifiers));
        $this->assertSame($this->events->getIdentifiers(), $identifiers);
        $identifiers[] = 'baz';
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->addIdentifiers($identifiers));
        $this->assertSame($this->events->getIdentifiers(), $identifiers);
    }

    public function testIdentifierGetterSettersWorkWithTraversables()
    {
        $identifiers = new ArrayIterator(array('foo', 'bar'));
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->setIdentifiers($identifiers));
        $this->assertSame($this->events->getIdentifiers(), (array) $identifiers);
        $identifiers = new ArrayIterator(array('foo', 'bar', 'baz'));
        $this->assertInstanceOf('Zend\EventManager\EventManager', $this->events->addIdentifiers($identifiers));
        $this->assertSame($this->events->getIdentifiers(), (array) $identifiers);
    }

    public function testListenersAttachedWithWildcardAreTriggeredForAllEvents()
    {
        $test     = new stdClass;
        $test->events = array();
        $callback = function ($e) use ($test) {
            $test->events[] = $e->getName();
        };

        $this->events->attach('*', $callback);
        foreach (array('foo', 'bar', 'baz') as $event) {
            $this->events->trigger($event);
            $this->assertContains($event, $test->events);
        }
    }

    public function testSettingSharedEventManagerSetsStaticEventManagerInstance()
    {
        $shared = new SharedEventManager();
        $this->events->setSharedManager($shared);
        $this->assertSame($shared, $this->events->getSharedManager());
        $this->assertSame($shared, StaticEventManager::getInstance());
    }

    public function testSharedEventManagerAttachReturnsCallbackHandler()
    {
        $shared = new SharedEventManager;
        $callbackHandler = $shared->attach(
            'foo',
            'bar',
            function ($e) {
                return true;
            }
        );
        $this->assertTrue($callbackHandler instanceof CallbackHandler);
    }

    public function testDoesNotCreateStaticInstanceIfNonePresent()
    {
        StaticEventManager::resetInstance();
        $this->assertFalse($this->events->getSharedManager());
    }
}
