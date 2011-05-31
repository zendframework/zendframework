<?php
namespace ZendTest\Di;

use Zend\Di\Reference;

use PHPUnit_Framework_TestCase as TestCase;

class ReferenceTest extends TestCase
{
    public function testReferenceReturnsNamePassedToConstructor()
    {
        $name = uniqid();
        $ref = new Reference($name);
        $this->assertEquals($name, $ref->getServiceName());
    }
}
