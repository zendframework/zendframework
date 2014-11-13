<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Stdlib\Exception\BadMethodCallException;
use ZendTest\Stdlib\TestAsset\ObjectProperty as ObjectPropertyTestAsset;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\ObjectProperty}
 *
 * @covers \Zend\Stdlib\Hydrator\ObjectProperty
 * @group Zend_Stdlib
 */
class ObjectPropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectProperty
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new ObjectProperty();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractException()
    {
        $this->setExpectedException('BadMethodCallException');
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateException()
    {
        $this->setExpectedException('BadMethodCallException');
        $this->hydrator->hydrate(array('some' => 'data'), 'thisIsNotAnObject');
    }

    /**
     * Verifies that the hydrator can extract from property of stdClass objects
     */
    public function testCanExtractFromStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $this->assertSame(array('foo' => 'bar'), $this->hydrator->extract($object));
    }

    /**
     * Verifies that the extraction process works on classes that aren't stdClass
     */
    public function testCanExtractFromClass()
    {
        $object = new ObjectPropertyTestAsset();
        $this->assertSame(
            array(
                'foo' => 'bar',
                'bar' => 'foo',
                'blubb' => 'baz'
            ),
            $this->hydrator->extract($object)
        );
    }

    /**
     * Verify hydration of stdClass
     */
    public function testCanHydrateStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $this->hydrator->hydrate(array('foo' => 'baz'), $object);

        $this->assertEquals('baz', $object->foo);
    }

    /**
     * Verify new properties are created if the object is stdClass
     */
    public function testCanHydrateExtrasToStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $this->hydrator->hydrate(array('foo' => 'baz', 'bar' => 'baz'), $object);

        $this->assertEquals('baz', $object->foo);
        $this->assertObjectHasAttribute('bar', $object);
        $this->assertAttributeContains('baz', 'bar', $object);
    }

    /**
     * Verify that can hydrate our class public properties
     */
    public function testCanHydrateClassPublicProperties()
    {
        $object = new ObjectPropertyTestAsset();

        $this->hydrator->hydrate(
            array(
                'foo' => 'foo',
                'bar' => 'bar',
                'blubb' => 'blubb',
                'quo' => 'quo',
                'quin' => 'quin'
            ),
            $object
        );

        $this->assertAttributeContains('foo', 'foo', $object);
        $this->assertAttributeContains('bar', 'bar', $object);
        $this->assertAttributeContains('blubb', 'blubb', $object);
        $this->assertAttributeContains('quo', 'quo', $object);
        $this->assertAttributeNotContains('quin', 'quin', $object);
    }

    /**
     * Verify that the hydrator hydrates the public property, but doesn't create new ones
     */
    public function testHydratorDoesNotCreatePropertiesOnClass()
    {
        $object = new ObjectPropertyTestAsset();

        $this->hydrator->hydrate(array('foo' => 'foo', 'baz' => 'foo'), $object);
        $this->assertObjectNotHasAttribute('baz', $object);
        $this->assertAttributeContains('foo', 'foo', $object);

    }
}
