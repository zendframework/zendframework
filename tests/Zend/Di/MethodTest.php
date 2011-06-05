<?php
namespace ZendTest\Di;

use Zend\Di\Method;

use PHPUnit_Framework_TestCase as TestCase;

class MethodTest extends TestCase
{
    public function testMethodReturnsNamePassedToConstructor()
    {
        $name   = uniqid();
        $method = new Method($name, array());
        $this->assertEquals($name, $method->getName());
    }

    public function testMethodReturnsParamsPassedToConstructor()
    {
        $name   = uniqid();
        $params   = array(
            'foo',
            new \stdClass,
            true,
            null,
            0,
            0.00,
            array('bar'),
        );
        $method = new Method($name, $params);
        $this->assertEquals($params, $method->getParams());
    }

    public function testNoClassSetByDefault()
    {
        $name   = uniqid();
        $method = new Method($name, array());
        $this->assertNull($method->getClass());
    }

    public function testClassIsMutable()
    {
        $name   = uniqid();
        $method = new Method($name, array());
        $method->setClass(__CLASS__);
        $this->assertEquals(__CLASS__, $method->getClass());
    }

    public function testParamsAreReturnedInOrderProvidedWhenNoParamMapGivenAndNoClassProvided()
    {
        $name   = uniqid();
        $method = new Method($name, array('bar' => 'BAR', 'foo' => 'FOO'));

        $params = $method->getParams();
        $this->assertEquals(array('BAR', 'FOO'), $params);
    }

    public function testParamsAreReturnedInArgOrderWhenNoParamMapGivenAndClassIsProvided()
    {
        $method = new Method('methodWithArgs', array('bar' => 'BAR', 'foo' => 'FOO'));
        $method->setClass($this);
        $params = $method->getParams();
        $this->assertEquals(array('FOO', 'BAR'), $params);
    }

    public function testParamsAreReturnedInParamMapOrderWhenNoClassProvided()
    {
        $method = new Method('methodWithArgs', array('bar' => 'BAR', 'foo' => 'FOO'), array('foo' => 0, 'bar' => 1));
        $params = $method->getParams();
        $this->assertEquals(array('FOO', 'BAR'), $params);
    }

    public function testParamsAreReturnedInParamMapOrderWhenClassProvided()
    {
        $method = new Method('methodWithArgs', array('bar' => 'BAR', 'foo' => 'FOO'), array('bar' => 0, 'foo' => 1));
        $method->setClass($this);
        $params = $method->getParams();
        $this->assertEquals(array('BAR', 'FOO'), $params);
    }

    public function testParamMapIsMutable()
    {
        $map    = array('bar' => 0, 'foo' => 1);
        $method = new Method('methodWithArgs', array('bar' => 'BAR', 'foo' => 'FOO'), $map);
        $this->assertEquals($map, $method->getParamMap());

        $map    = array('foo' => 0, 'bar' => 1);
        $method->setParamMap($map);
        $this->assertEquals($map, $method->getParamMap());
    }

    public function methodWithArgs($foo, $bar)
    {
    }
}
