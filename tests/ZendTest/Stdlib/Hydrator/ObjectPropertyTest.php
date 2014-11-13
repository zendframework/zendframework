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
     * @expectedException BadMethodCallException
     */
    public function testHydratorExtractException()
    {
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testHydratorHydrateException()
    {
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
        $object = new DummyObjectForObjectPropertyTest();
        $this->assertSame(array('foo' => 'bar'), $this->hydrator->extract($object));
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
        $this->assertOjectHasAttribute('bar', $object);
        $this->assertAttributeContains('baz', $object, 'bar');
    }

    /**
     * Verify that can hydrate our class public properties
     */
    public function testCanHydrateClassPublicProperties()
    {
        $object = new DummyObjectForObjectPropertyTest();

        $this->hydrator->hydrate(array('foo' => 'baz', 'bar' => 'foo'), $object);

        $this->assertAttributeContains('baz', $object, 'foo');
        $this->assertAttributeContains('baz', $object, 'bar');
        $this->assertAttributeNotContains('foo', $object, 'bar');
    }

    /**
     * Verify that the hydrator hydrates the public property, but doesn't create new ones
     */
    public function testHydratorDoesNotCreatePropertiesOnClass()
    {
        $object = new DummyObjectForObjectPropertyTest();

        $this->hydrator->hydrate(array('foo' => 'baz', 'baz' => 'foo'), $object);
        $this->assertObjectNotHasAttribute('baz', $object);
        $this->assertAttributeContains('baz', $object, 'foo');

    }
}


class DummyObjectForObjectPropertyTest
{
    public $foo = 'bar';
    protected $bar = 'baz';
}
