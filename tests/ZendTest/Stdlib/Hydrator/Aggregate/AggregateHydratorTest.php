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
use stdClass;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator}
 */
class AggregateHydratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator
     */
    protected $hydrator;

    /**
     * @var \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $attached = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');

        $this
            ->eventManager
            ->expects($this->once())
            ->method('attachAggregate')
            ->with($this->isInstanceOf('Zend\Stdlib\Hydrator\Aggregate\HydratorListener'), 123);

        $this->hydrator->add($attached, 123);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Zend\Stdlib\Hydrator\Aggregate\HydrateEvent'));

        $this->assertSame($object, $this->hydrator->hydrate(array('foo' => 'bar'), $object));
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Zend\Stdlib\Hydrator\Aggregate\ExtractEvent'));

        $this->assertSame(array(), $this->hydrator->extract($object));
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $hydrator->getEventManager());

        $eventManager
            ->expects($this->once())
            ->method('setIdentifiers')
            ->with(
                array(
                     'Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator',
                     'Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator',
                )
            );

        $hydrator->setEventManager($eventManager);

        $this->assertSame($eventManager, $hydrator->getEventManager());
    }
}
