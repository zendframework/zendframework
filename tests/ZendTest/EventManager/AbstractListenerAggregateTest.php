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

use ZendTest\EventManager\TestAsset\MockAbstractListenerAggregate;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class AbstractListenerAggregateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ZendTest\EventManager\TestAsset\MockAbstractListenerAggregate
     */
    protected $listener;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->listener = new MockAbstractListenerAggregate();
    }

    /**
     * @covers \Zend\EventManager\AbstractListenerAggregate::detach
     */
    public function testDetach()
    {
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

        $this->listener->attach($eventManager);
        $this->assertSame($callbackHandlers, $this->listener->getCallbacks());

        $this->listener->detach($unrelatedEventManager);

        $this->assertSame($callbackHandlers, $this->listener->getCallbacks());

        $eventManager
            ->expects($this->exactly(2))
            ->method('detach')
            ->with($this->callback(function ($callbackHandler) use ($callbackHandlers) {
                return in_array($callbackHandler, $callbackHandlers, true);
            }))
            ->will($this->returnValue(true));

        $this->listener->detach($eventManager);
        $this->assertEmpty($this->listener->getCallbacks());
    }
}
