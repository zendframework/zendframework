<?php

namespace ZendTest\Docbook;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Docbook\ClassParser,
    Zend\Code\Reflection\ClassReflection;

class ClassParserTest extends TestCase
{
    public function setUp()
    {
        $this->class  = new ClassReflection(new TestAsset\ParsedClass());
        $this->parser = new ClassParser($this->class);
    }

    public function testIdShouldBeNormalizedNamespacedClass()
    {
        $id = $this->parser->getId();
        $this->assertEquals('zend-test.docbook.test-asset.parsed-class', $id);
    }

    public function testRetrievingMethodsShouldReturnClassMethodObjects()
    {
        $methods = $this->parser->getMethods();

        $this->assertEquals(count($this->class->getMethods(\ReflectionMethod::IS_PUBLIC)), count($methods));
        foreach ($methods as $method) {
            $this->assertInstanceOf('Zend\Docbook\ClassMethod', $method);
        }
    }
}
