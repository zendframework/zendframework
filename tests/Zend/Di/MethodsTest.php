<?php
namespace ZendTest\Di;

use Zend\Di\Methods,
    Zend\Di\Method;

use PHPUnit_Framework_TestCase as TestCase;

class MethodsTest extends TestCase
{
    public function setUp()
    {
        $this->methods = new Methods();
    }

    public function testInsertAcceptsMethodObject()
    {
        $this->methods->insert(new Method('foo', array()));
        // No test; simply ensuring no exceptions/errors
    }

    public function invalidMethodArguments()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(1),
            array(1.0),
            array(array()),
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider invalidMethodArguments
     */
    public function testInsertRaisesExceptionIfInvalidArgumentProvidedForMethodObject($arg)
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->methods->insert($arg);
    }

    public function testIterationReturnsMethodNameForKey()
    {
        $method = new Method('foo', array());
        $this->methods->insert($method);
        foreach ($this->methods as $key => $value) {
        }
        $this->assertEquals('foo', $key);
    }

    public function testIterationReturnsMethodObjectForValue()
    {
        $method = new Method('foo', array());
        $this->methods->insert($method);
        foreach ($this->methods as $key => $value) {
        }
        $this->assertEquals($method, $value);
    }
}
