<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\EventManager;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\EventManager\EventManager;

class EventManagerAwareTraitTest extends TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testSetEventManager()
    {
        $object = new TestAsset\MockEventManagerAwareTrait;

        $this->assertAttributeEquals(null, 'eventManager', $object);

        $eventManager = new EventManager;

        $object->setEventManager($eventManager);

        $this->assertAttributeEquals($eventManager, 'eventManager', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testGetEventManager()
    {
        $object = new TestAsset\MockEventManagerAwareTrait;

        $this->assertEquals(null, $object->getEventManager());

        $eventManager = new EventManager;

        $object->setEventManager($eventManager);

        $this->assertEquals($eventManager, $object->getEventManager());
    }
}
