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

    public function testMethodReturnsArgsPassedToConstructor()
    {
        $name   = uniqid();
        $args   = array(
            'foo',
            new \stdClass,
            true,
            null,
            0,
            0.00,
            array('bar'),
        );
        $method = new Method($name, $args);
        $this->assertEquals($args, $method->getArgs());
    }
}
