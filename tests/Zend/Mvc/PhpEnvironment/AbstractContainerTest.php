<?php

namespace ZendTest\Mvc\PhpEnvironment;

use PHPUnit_Framework_TestCase as TestCase;

abstract class AbstractContainerTest extends TestCase
{
    public $originalValues = array(
        'test'  => 'test value',
        'test2' => 2,
        'test3' => 3.0,
    );

    abstract public function testChangesInContainerPropagateToSuperGlobal();

    public function testToStringSerializesToHTTPEncodedString()
    {
        $string = http_build_query($this->originalValues);
        $this->assertEquals($string, $this->container->toString());
    }

    public function testFromStringOverwritesOriginalValues()
    {
        $original = $this->container->toArray();
        if (!$original == $this->originalValues) {
            $this->fail('Container does not appear to be seeded with original values?');
        }

        $test = array(
            'foo' => 'bar',
            'baz' => 'bat',
        );
        $string = http_build_query($test);

        $this->container->fromString($string);

        $this->assertEquals($test, $this->container->toArray());
    }

    public function testFromArrayOverwritesOriginalValues()
    {
        $original = $this->container->toArray();
        if (!$original == $this->originalValues) {
            $this->fail('Container does not appear to be seeded with original values?');
        }

        $test = array(
            'foo' => 'bar',
            'baz' => 'bat',
        );

        $this->container->fromArray($test);

        $this->assertEquals($test, $this->container->toArray());
    }

    public function testGetReturnsValueIfItExists()
    {
        $this->container->set('foo-bar-baz', 'bat');
        $value = $this->container->get('foo-bar-baz');
        $this->assertEquals('bat', $value);
    }

    public function testGetReturnsDefaultValueIfNoKeyExists()
    {
        $value = $this->container->get('foo-bar-baz', 'default');
        $this->assertEquals('default', $value);
    }

    public function testCanSetAndGetViaArrayAccess()
    {
        $this->assertFalse(isset($this->container['foo-bar-baz']));
        $this->assertNull($this->container['foo-bar-baz']);
        $this->container['foo-bar-baz'] = 'bat';
        $this->assertTrue(isset($this->container['foo-bar-baz']));
        $this->assertEquals('bat', $this->container['foo-bar-baz']);
        unset($this->container['foo-bar-baz']);
        $this->assertFalse(isset($this->container['foo-bar-baz']));
        $this->assertNull($this->container['foo-bar-baz']);
    }

    public function testIsIterable()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->container);
        $this->assertTrue(method_exists($this->container, 'getIterator'));
        $iterator = $this->container->getIterator();
        $this->assertInstanceOf('Iterator', $iterator);
    }

    public function testCanIterateContainer()
    {
        $values = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'bat' => 'bogus',
        );
        $this->container->fromArray($values);

        foreach ($this->container as $key => $value) {
            $this->assertArrayHasKey($key, $values);
            $this->assertTrue(isset($this->container[$key])); // not an array, so array-has-key won't work
            $this->assertEquals($values[$key], $value);
            $this->assertEquals($values[$key], $this->container[$key]);
        }
    }
}
