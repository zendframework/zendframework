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
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use stdClass;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Aggregate\HydrateEvent}
 */
class HydrateEventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\HydrateEvent
     */
    public function testEvent()
    {
        $target    = new \stdClass();
        $hydrated1 = new \stdClass();
        $data1     = array('president' => 'Zaphod');
        $event     = new HydrateEvent($target, $hydrated1, $data1);
        $data2     = array('maintainer' => 'Marvin');
        $hydrated2 = new \stdClass();

        $this->assertSame($target, $event->getTarget());
        $this->assertSame($hydrated1, $event->getHydratedObject());
        $this->assertSame($data1, $event->getHydrationData());

        $event->setHydrationData($data2);

        $this->assertSame($data2, $event->getHydrationData());


        $event->setHydratedObject($hydrated2);

        $this->assertSame($hydrated2, $event->getHydratedObject());
    }
}