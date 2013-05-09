<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Aggregate;

use PHPUnit_Framework_TestCase;
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;
use Zend\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydratorListener;
use stdClass;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Aggregate\HydratorListener}
 */
class HydratorListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrator;

    /**
     * @var \Zend\Stdlib\Hydrator\Aggregate\HydratorListener
     */
    protected $listener;

    /**
     * {@inheritDoc}
     *
     * @covers \Zend\Stdlib\Hydrator\Aggregate\HydratorListener::__construct
     */
    public function setUp()
    {
        $this->hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');
        $this->listener = new HydratorListener($this->hydrator);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\HydratorListener::attach
     */
    public function testAttach()
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->with(
                $this->logicalOr(HydrateEvent::EVENT_HYDRATE, ExtractEvent::EVENT_EXTRACT),
                $this->logicalAnd(
                    $this->callback('is_callable'),
                    $this->logicalOr(array($this->listener, 'onHydrate'), array($this->listener, 'onExtract'))
                )
            );

        $this->listener->attach($eventManager);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\HydratorListener::onHydrate
     */
    public function testOnHydrate()
    {
        $object   = new stdClass();
        $hydrated = new stdClass();
        $data     = array('foo' => 'bar');
        $event    = $this
            ->getMockBuilder('Zend\Stdlib\Hydrator\Aggregate\HydrateEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())->method('getHydratedObject')->will($this->returnValue($object));
        $event->expects($this->any())->method('getHydrationData')->will($this->returnValue($data));

        $this
            ->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($data, $object)
            ->will($this->returnValue($hydrated));
        $event->expects($this->once())->method('setHydratedObject')->with($hydrated);

        $this->assertSame($hydrated, $this->listener->onHydrate($event));
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\HydratorListener::onExtract
     */
    public function testOnExtract()
    {
        $object = new stdClass();
        $data   = array('foo' => 'bar');
        $event  = $this
            ->getMockBuilder('Zend\Stdlib\Hydrator\Aggregate\ExtractEvent')
            ->disableOriginalConstructor()
            ->getMock();


        $event->expects($this->any())->method('getExtractionObject')->will($this->returnValue($object));

        $this
            ->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($object)
            ->will($this->returnValue($data));
        $event->expects($this->once())->method('mergeExtractedData')->with($data);

        $this->assertSame($data, $this->listener->onExtract($event));
    }
}