<?php

namespace ZendTest\Docbook;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Docbook\ClassMethod,
    Zend\Code\Reflection\ClassReflection;

class ClassMethodTest extends TestCase
{
    /**
     * @var ClassReflection
     */
    protected $class;

    public function setUp()
    {
        $this->class  = new ClassReflection(new TestAsset\ParsedClass());
    }

    public function testCorrectlyDetectsMethodName()
    {
        $r = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('action1', $method->getName());
    }

    public function testIdShouldBeNormalizedMethodName()
    {
        $r = $this->class->getMethod('camelCasedMethod');
        $method = new ClassMethod($r);

        $this->assertEquals('zend-test.docbook.test-asset.parsed-class.methods.camel-cased-method', $method->getId());
    }

    public function testCorrectlyDetectsMethodShortDescription()
    {
        $r = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertContains('short action1 method description', $method->getShortDescription());
        $this->assertNotContains('Long description for action1', $method->getShortDescription());
        $this->assertNotRegExp('/\*\s*/', $method->getShortDescription());
    }

    public function testCorrectlyDetectsMethodLongDescription()
    {
        $r = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertContains('Long description for action1', $method->getLongDescription());
        $this->assertNotContains('short action1 method description', $method->getLongDescription());
        $this->assertNotRegExp('/\*\s*/', $method->getLongDescription());
    }

    public function testCorrectlyDeterminesNonObjectReturnType()
    {
        $r = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('float', $method->getReturnType());
    }

    /**
     * @group prototype
     */
    public function testCorrectlyBuildsNonObjectArgumentPrototype()
    {
        $r = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('string $arg1, bool $arg2, null|array $arg3', $method->getPrototype());
    }

    public function testCorrectlyDeterminesReturnTypeClass()
    {
        $r = $this->class->getMethod('action2');
        $method = new ClassMethod($r);

        $this->assertEquals('ZendTest\Docbook\TestAsset\ParsedClass', $method->getReturnType());
    }

    /**
     * @group prototype
     */
    public function testCorrectlyBuildsArgumentPrototypeContainingClassNames()
    {
        $r = $this->class->getMethod('action2');
        $method = new ClassMethod($r);

        $this->assertEquals('null|Zend\Loader\PluginClassLoader $loader', $method->getPrototype());
    }
}
