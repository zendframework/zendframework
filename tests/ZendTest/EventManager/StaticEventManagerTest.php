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

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\EventManager\EventManager;
use Zend\EventManager\StaticEventManager;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class StaticEventManagerTest extends TestCase
{
    public function setUp()
    {
        StaticEventManager::resetInstance();
    }

    public function tearDown()
    {
        StaticEventManager::resetInstance();
    }

    public function testOperatesAsASingleton()
    {
        $expected = StaticEventManager::getInstance();
        $test     = StaticEventManager::getInstance();
        $this->assertSame($expected, $test);
    }

    public function testCanResetInstance()
    {
        $original = StaticEventManager::getInstance();
        StaticEventManager::resetInstance();
        $test = StaticEventManager::getInstance();
        $this->assertNotSame($original, $test);
    }

    public function testSingletonInstanceIsInstanceOfClass()
    {
        $this->assertInstanceOf('Zend\EventManager\StaticEventManager', StaticEventManager::getInstance());
    }

    public function testCanAttachCallbackToEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $events->getEvents('foo'));
        $expected  = array($this, __FUNCTION__);
        $found     = false;
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
        $this->assertTrue(0 < count($listeners), 'Empty listeners!');
        foreach ($listeners as $listener) {
            if ($expected === $listener->getCallback()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Did not find listener!');
    }

    public function testCanAttachCallbackToMultipleEventsAtOnce()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bar', array('foo', 'test'), array($this, __FUNCTION__));
        $this->assertContains('foo', $events->getEvents('bar'));
        $this->assertContains('test', $events->getEvents('bar'));
        $expected = array($this, __FUNCTION__);
        foreach (array('foo', 'test') as $event) {
            $found     = false;
            $listeners = $events->getListeners('bar', $event);
            $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
            $this->assertTrue(0 < count($listeners), 'Empty listeners!');
            foreach ($listeners as $listener) {
                if ($expected === $listener->getCallback()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Did not find listener!');
        }
    }

    public function testCanAttachSameEventToMultipleResourcesAtOnce()
    {
        $events = StaticEventManager::getInstance();
        $events->attach(array('foo', 'test'), 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $events->getEvents('foo'));
        $this->assertContains('bar', $events->getEvents('test'));
        $expected = array($this, __FUNCTION__);
        foreach (array('foo', 'test') as $id) {
            $found     = false;
            $listeners = $events->getListeners($id, 'bar');
            $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
            $this->assertTrue(0 < count($listeners), 'Empty listeners!');
            foreach ($listeners as $listener) {
                if ($expected === $listener->getCallback()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Did not find listener!');
        }
    }

    public function testCanAttachCallbackToMultipleEventsOnMultipleResourcesAtOnce()
    {
        $events = StaticEventManager::getInstance();
        $events->attach(array('bar', 'baz'), array('foo', 'test'), array($this, __FUNCTION__));
        $this->assertContains('foo', $events->getEvents('bar'));
        $this->assertContains('test', $events->getEvents('bar'));
        $expected = array($this, __FUNCTION__);
        foreach (array('bar', 'baz') as $resource) {
            foreach (array('foo', 'test') as $event) {
                $found     = false;
                $listeners = $events->getListeners($resource, $event);
                $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
                $this->assertTrue(0 < count($listeners), 'Empty listeners!');
                foreach ($listeners as $listener) {
                    if ($expected === $listener->getCallback()) {
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'Did not find listener!');
            }
        }
    }

    public function testListenersAttachedUsingWildcardEventWillBeTriggeredByResource()
    {
        $test     = new stdClass;
        $test->events = array();
        $callback = function ($e) use ($test) {
            $test->events[] = $e->getName();
        };

        $staticEvents = StaticEventManager::getInstance();
        $staticEvents->attach('bar', '*', $callback);

        $events = new EventManager('bar');
        $events->setSharedManager($staticEvents);

        foreach (array('foo', 'bar', 'baz') as $event) {
            $events->trigger($event);
            $this->assertContains($event, $test->events);
        }
    }

    public function testCanDetachListenerFromResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        foreach ($events->getListeners('foo', 'bar') as $listener) {
            // only one; retrieving it so we can detach
        }
        $events->detach('foo', $listener);
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertEquals(0, count($listeners));
    }

    public function testCanGetEventsByResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $this->assertEquals(array('bar'), $events->getEvents('foo'));
    }

    public function testCanGetEventsByWildcard()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('*', 'bar', array($this, __FUNCTION__));
        $this->assertEquals(array('bar'), $events->getEvents('foo'));
    }

    public function testCanGetListenersByResourceAndEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
        $this->assertEquals(1, count($listeners));
    }

    public function testCanNotGetListenersByResourceAndEventWithWildcard()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('*', 'bar', array($this, __FUNCTION__));
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertFalse($listeners);
    }

    public function testCanGetListenersByWildcardAndEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('*', 'bar', array($this, __FUNCTION__));
        $listeners = $events->getListeners('*', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
        $this->assertEquals(1, count($listeners));
    }

    public function testCanClearListenersByResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $events->attach('foo', 'baz', array($this, __FUNCTION__));
        $events->clearListeners('foo');
        $this->assertFalse($events->getListeners('foo', 'bar'));
        $this->assertFalse($events->getListeners('foo', 'baz'));
    }

    public function testCanClearListenersByResourceAndEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $events->attach('foo', 'baz', array($this, __FUNCTION__));
        $events->attach('foo', 'bat', array($this, __FUNCTION__));
        $events->clearListeners('foo', 'baz');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'baz'));
        $this->assertEquals(0, count($events->getListeners('foo', 'baz')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'bar'));
        $this->assertEquals(1, count($events->getListeners('foo', 'bar')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'bat'));
        $this->assertEquals(1, count($events->getListeners('foo', 'bat')));
    }

    public function testCanPassArrayOfIdentifiersToConstructor()
    {
        $identifiers = array('foo', 'bar');
        $manager = new EventManager($identifiers);
    }

    public function testListenersAttachedToAnyIdentifierProvidedToEventManagerWillBeTriggered()
    {
        $identifiers = array('foo', 'bar');
        $events  = StaticEventManager::getInstance();
        $manager = new EventManager($identifiers);
        $manager->setSharedManager($events);

        $test = new \stdClass;
        $test->triggered = 0;
        $events->attach('foo', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $events->attach('bar', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $manager->trigger('bar', $this, array());
        $this->assertEquals(2, $test->triggered);
    }

    public function testListenersAttachedToWildcardsWillBeTriggered()
    {
        $identifiers = array('foo', 'bar');
        $events  = StaticEventManager::getInstance();
        $manager = new EventManager($identifiers);
        $manager->setSharedManager($events);

        $test = new \stdClass;
        $test->triggered = 0;
        $events->attach('*', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        //Tests one can have multiple wildcards attached
        $events->attach('*', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $manager->trigger('bar', $this, array());
        $this->assertEquals(2, $test->triggered);
    }

    public function testListenersAttachedToAnyIdentifierProvidedToEventManagerOrWildcardsWillBeTriggered()
    {
        $identifiers = array('foo', 'bar');
        $events  = StaticEventManager::getInstance();
        $manager = new EventManager($identifiers);
        $manager->setSharedManager($events);

        $test = new \stdClass;
        $test->triggered = 0;
        $events->attach('foo', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $events->attach('bar', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $events->attach('*', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        //Tests one can have multiple wildcards attached
        $events->attach('*', 'bar', function ($e) use ($test) {
            $test->triggered++;
        });
        $manager->trigger('bar', $this, array());
        $this->assertEquals(4, $test->triggered);
    }

    public function testCanAttachListenerAggregate()
    {
        $staticManager = StaticEventManager::getInstance();
        $aggregate = new TestAsset\SharedMockAggregate('bazinga');
        $staticManager->attachAggregate($aggregate);

        $events = $staticManager->getEvents('bazinga');
        $this->assertCount(2, $events);
    }

    public function testCanDetachListenerAggregate()
    {
        $staticManager = StaticEventManager::getInstance();
        $aggregate = new TestAsset\SharedMockAggregate('bazinga');

        $staticManager->attachAggregate($aggregate);
        $events = $staticManager->getEvents('bazinga');
        $this->assertCount(2, $events);

        $staticManager->detachAggregate($aggregate);
        $events = $staticManager->getEvents('bazinga');
        $this->assertCount(0, $events);
    }
}
