<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Stdlib\Exception\BadMethodCallException;
use ZendTest\Stdlib\TestAsset\ClassWithPublicStaticProperties;
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
    private $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->hydrator = new ObjectProperty();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractThrowsExceptionOnNonObjectParameter()
    {
        $this->setExpectedException('BadMethodCallException');
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateThrowsExceptionOnNonObjectParameter()
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
    public function testCanExtractFromGenericClass()
    {
        $this->assertSame(
            array(
                'foo' => 'bar',
                'bar' => 'foo',
                'blubb' => 'baz',
                'quo' => 'blubb'
            ),
            $this->hydrator->extract(new ObjectPropertyTestAsset())
        );
    }

    /**
     * Verify hydration of {@see \stdClass}
     */
    public function testCanHydrateStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(array('foo' => 'baz'), $object);

        $this->assertEquals('baz', $object->foo);
    }

    /**
     * Verify that new properties are created if the object is stdClass
     */
    public function testCanHydrateAdditionalPropertiesToStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(array('foo' => 'baz', 'bar' => 'baz'), $object);

        $this->assertEquals('baz', $object->foo);
        $this->assertObjectHasAttribute('bar', $object);
        $this->assertAttributeSame('baz', 'bar', $object);
    }

    /**
     * Verify that it can hydrate our class public properties
     */
    public function testCanHydrateGenericClassPublicProperties()
    {
        $object = $this->hydrator->hydrate(
            array(
                'foo' => 'foo',
                'bar' => 'bar',
                'blubb' => 'blubb',
                'quo' => 'quo',
                'quin' => 'quin'
            ),
            new ObjectPropertyTestAsset()
        );

        $this->assertAttributeSame('foo', 'foo', $object);
        $this->assertAttributeSame('bar', 'bar', $object);
        $this->assertAttributeSame('blubb', 'blubb', $object);
        $this->assertAttributeSame('quo', 'quo', $object);
        $this->assertAttributeNotSame('quin', 'quin', $object);
    }

    /**
     * Verify that it can hydrate new properties on generic classes
     */
    public function testCanHydrateGenericClassNonExistingProperties()
    {
        $object = $this->hydrator->hydrate(array('newProperty' => 'newPropertyValue'), new ObjectPropertyTestAsset());

        $this->assertAttributeSame('newPropertyValue', 'newProperty', $object);
    }

    /**
     * Verify that hydration is skipped for class properties (it is an object hydrator after all)
     */
    public function testSkipsPublicStaticClassPropertiesHydration()
    {
        $this->hydrator->hydrate(
            array('foo' => '1', 'bar' => '2', 'baz' => '3'),
            new ClassWithPublicStaticProperties()
        );

        $this->assertSame('foo', ClassWithPublicStaticProperties::$foo);
        $this->assertSame('bar', ClassWithPublicStaticProperties::$bar);
        $this->assertSame('baz', ClassWithPublicStaticProperties::$baz);
    }

    /**
     * Verify that extraction is skipped for class properties (it is an object hydrator after all)
     */
    public function testSkipsPublicStaticClassPropertiesExtraction()
    {
        $this->assertEmpty($this->hydrator->extract(new ClassWithPublicStaticProperties()));
    }
}
