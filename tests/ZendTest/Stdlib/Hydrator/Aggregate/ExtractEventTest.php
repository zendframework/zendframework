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
use stdClass;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Aggregate\ExtractEvent}
 */
class ExtractEventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\ExtractEvent
     */
    public function testEvent()
    {
        $target    = new stdClass();
        $object1   = new stdClass();
        $event     = new ExtractEvent($target, $object1);
        $data2     = array('maintainer' => 'Marvin');
        $object2   = new stdClass();

        $this->assertSame(ExtractEvent::EVENT_EXTRACT, $event->getName());
        $this->assertSame($target, $event->getTarget());
        $this->assertSame($object1, $event->getExtractionObject());
        $this->assertSame(array(), $event->getExtractedData());

        $event->setExtractedData($data2);

        $this->assertSame($data2, $event->getExtractedData());


        $event->setExtractionObject($object2);

        $this->assertSame($object2, $event->getExtractionObject());

        $event->mergeExtractedData(array('president' => 'Zaphod'));

        $extracted = $event->getExtractedData();

        $this->assertCount(2, $extracted);
        $this->assertSame('Marvin', $extracted['maintainer']);
        $this->assertSame('Zaphod', $extracted['president']);
    }
}
