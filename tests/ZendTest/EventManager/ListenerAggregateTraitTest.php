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

use ZendTest\EventManager\TestAsset\MockListenerAggregateTrait;

/**
 * @requires PHP 5.4
 */
class ListenerAggregateTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zend\EventManager\ListenerAggregateTrait::detach
     */
    public function testDetach()
    {
        $listener              = new MockListenerAggregateTrait();
        $eventManager          = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $unrelatedEventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $callbackHandlers      = array();
        $test                  = $this;

        $eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->will($this->returnCallback(function () use (&$callbackHandlers, $test) {
                return $callbackHandlers[] = $test->getMock('Zend\\Stdlib\\CallbackHandler', array(), array(), '', false);
            }));

        $listener->attach($eventManager);
        $this->assertSame($callbackHandlers, $listener->getCallbacks());

        $listener->detach($unrelatedEventManager);

        $this->assertSame($callbackHandlers, $listener->getCallbacks());

        $eventManager
            ->expects($this->exactly(2))
            ->method('detach')
            ->with($this->callback(function ($callbackHandler) use ($callbackHandlers) {
                return in_array($callbackHandler, $callbackHandlers, true);
            }))
            ->will($this->returnValue(true));

        $listener->detach($eventManager);
        $this->assertEmpty($listener->getCallbacks());
    }
}
