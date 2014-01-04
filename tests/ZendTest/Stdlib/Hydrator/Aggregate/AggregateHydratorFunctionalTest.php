<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Aggregate;


use ArrayObject;
use PHPUnit_Framework_TestCase;
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;
use Zend\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;
use ZendTest\Stdlib\TestAsset\AggregateObject;
use stdClass;

/**
 * Integration tests {@see \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator}
 */
class AggregateHydratorFunctionalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new AggregateHydrator();
    }

    /**
     * Verifies that no interaction happens when the aggregate hydrator is empty
     */
    public function testEmptyAggregate()
    {
        $object = new ArrayObject(array('zaphod' => 'beeblebrox'));

        $this->assertSame(array(), $this->hydrator->extract($object));
        $this->assertSame($object, $this->hydrator->hydrate(array('arthur' => 'dent'), $object));

        $this->assertSame(array('zaphod' => 'beeblebrox'), $object->getArrayCopy());
    }

    /**
     * @dataProvider getHydratorSet
     *
     * Verifies that using a single hydrator will have the aggregate hydrator behave like that single hydrator
     */
    public function testSingleHydratorExtraction(HydratorInterface $comparisonHydrator, $object)
    {
        $blueprint = clone $object;

        $this->hydrator->add($comparisonHydrator);

        $this->assertSame($comparisonHydrator->extract($blueprint), $this->hydrator->extract($object));
    }

    /**
     * @dataProvider getHydratorSet
     *
     * Verifies that using a single hydrator will have the aggregate hydrator behave like that single hydrator
     */
    public function testSingleHydratorHydration(HydratorInterface $comparisonHydrator, $object, $data)
    {
        $blueprint = clone $object;

        $this->hydrator->add($comparisonHydrator);

        $hydratedBlueprint = $comparisonHydrator->hydrate($data, $blueprint);
        $hydrated          = $this->hydrator->hydrate($data, $object);

        $this->assertEquals($hydratedBlueprint, $hydrated);

        if ($hydratedBlueprint === $blueprint) {
            $this->assertSame($hydrated, $object);
        }
    }

    /**
     * Verifies that multiple hydrators in an aggregate merge the extracted data
     */
    public function testExtractWithMultipleHydrators()
    {
        $this->hydrator->add(new ClassMethods());
        $this->hydrator->add(new ArraySerializable());

        $object = new AggregateObject();

        $extracted = $this->hydrator->extract($object);

        $this->assertArrayHasKey('maintainer', $extracted);
        $this->assertArrayHasKey('president', $extracted);
        $this->assertSame('Marvin', $extracted['maintainer']);
        $this->assertSame('Zaphod', $extracted['president']);
    }

    /**
     * Verifies that multiple hydrators in an aggregate merge the extracted data
     */
    public function testHydrateWithMultipleHydrators()
    {
        $this->hydrator->add(new ClassMethods());
        $this->hydrator->add(new ArraySerializable());

        $object = new AggregateObject();

        $this->assertSame(
            $object,
            $this->hydrator->hydrate(array('maintainer' => 'Trillian', 'president' => '???'), $object)
        );

        $this->assertArrayHasKey('maintainer', $object->arrayData);
        $this->assertArrayHasKey('president', $object->arrayData);
        $this->assertSame('Trillian', $object->arrayData['maintainer']);
        $this->assertSame('???', $object->arrayData['president']);
        $this->assertSame('Trillian', $object->maintainer);
    }

    /**
     * Verifies that stopping propagation within a listener in the hydrator allows modifying how the
     * hydrator behaves
     */
    public function testStoppedPropagationInExtraction()
    {
        $object   = new ArrayObject(array('president' => 'Zaphod'));
        $callback = function (ExtractEvent $event) {
            $event->setExtractedData(array('Ravenous Bugblatter Beast of Traal'));
            $event->stopPropagation();
        };

        $this->hydrator->add(new ArraySerializable());
        $this->hydrator->getEventManager()->attach(ExtractEvent::EVENT_EXTRACT, $callback, 1000);

        $this->assertSame(array('Ravenous Bugblatter Beast of Traal'), $this->hydrator->extract($object));
    }

    /**
     * Verifies that stopping propagation within a listener in the hydrator allows modifying how the
     * hydrator behaves
     */
    public function testStoppedPropagationInHydration()
    {
        $object        = new ArrayObject();
        $swappedObject = new stdClass();
        $callback = function (HydrateEvent $event) use ($swappedObject) {
            $event->setHydratedObject($swappedObject);
            $event->stopPropagation();
        };

        $this->hydrator->add(new ArraySerializable());
        $this->hydrator->getEventManager()->attach(HydrateEvent::EVENT_HYDRATE, $callback, 1000);

        $this->assertSame($swappedObject, $this->hydrator->hydrate(array('president' => 'Zaphod'), $object));
    }

    /**
     * Data provider method
     *
     * @return array
     */
    public function getHydratorSet()
    {
        return array(
            array(new ArraySerializable(), new ArrayObject(array('zaphod' => 'beeblebrox')), array('arthur' => 'dent')),
        );
    }
}
