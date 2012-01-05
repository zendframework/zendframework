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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\EventManager;
use Zend\EventManager\GlobalEventManager,
    Zend\EventManager\EventManager;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GlobalEventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        GlobalEventManager::setEventCollection(null);
    }

    public function testStoresAnEventManagerInstanceByDefault()
    {
        $events = GlobalEventManager::getEventCollection();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $events);
    }

    public function testPassingNullValueForEventCollectionResetsInstance()
    {
        $events = GlobalEventManager::getEventCollection();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $events);
        GlobalEventManager::setEventCollection(null);
        $events2 = GlobalEventManager::getEventCollection();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $events2);
        $this->assertNotSame($events, $events2);
    }

    public function testProxiesAllStaticOperationsToEventCollectionInstance()
    {
        $test    = new \stdClass();
        $listener = GlobalEventManager::attach('foo.bar', function ($e) use ($test) {
            $test->event  = $e->getName();
            $test->target = $e->getTarget();
            $test->params = $e->getParams();
            return $test->params;
        });
        $this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener);

        GlobalEventManager::trigger('foo.bar', $this, array('foo' => 'bar'));
        $this->assertSame($this, $test->target);
        $this->assertEquals('foo.bar', $test->event);
        $this->assertEquals(array('foo' => 'bar'), $test->params);

        $results = GlobalEventManager::triggerUntil('foo.bar', $this, array('baz' => 'bat'), function ($r) {
            return is_array($r);
        });
        $this->assertTrue($results->stopped());
        $this->assertEquals(array('baz' => 'bat'), $test->params);
        $this->assertEquals(array('baz' => 'bat'), $results->last());

        $events = GlobalEventManager::getEvents();
        $this->assertEquals(array('foo.bar'), $events);

        $listeners = GlobalEventManager::getListeners('foo.bar');
        $this->assertEquals(1, count($listeners));
        $this->assertTrue($listeners->contains($listener));

        GlobalEventManager::detach($listener);
        $events = GlobalEventManager::getEvents();
        $this->assertEquals(array(), $events);

        $listener = GlobalEventManager::attach('foo.bar', function ($e) use ($test) {
            $test->event  = $e->getEvent();
            $test->target = $e->getTarget();
            $test->params = $e->getParams();
        });
        $events = GlobalEventManager::getEvents();
        $this->assertEquals(array('foo.bar'), $events);
        GlobalEventManager::clearListeners('foo.bar');
        $events = GlobalEventManager::getEvents();
        $this->assertEquals(array(), $events);
    }
}
