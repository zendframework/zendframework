<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\BuilderDefinition,
    Zend\Di\Definition\Builder,
    PHPUnit_Framework_TestCase as TestCase;

class BuilderDefinitionTest extends TestCase
{
    
    public function testBuilderImplementsDefinition()
    {
        $builder = new BuilderDefinition();
        $this->assertInstanceOf('Zend\Di\Definition', $builder);
    }
    
    public function testBuilderCanBuildClassWithMethods()
    {
        $class = new Builder\PhpClass();
        $class->setName('Foo');
        $class->addSuperType('Parent');
        
        $injectionMethod = new Builder\InjectionMethod();
        $injectionMethod->setName('injectBar');
        $injectionMethod->addParameter('bar', 'Bar');
        
        $class->addInjectionMethod($injectionMethod);
        
        $definition = new BuilderDefinition();
        $definition->addClass($class);
        
        $this->assertTrue($definition->hasClass('Foo'));
        $this->assertEquals('__construct', $definition->getInstantiator('Foo'));
        $this->assertContains('Parent', $definition->getClassSupertypes('Foo'));
        $this->assertTrue($definition->hasInjectionMethods('Foo'));
        $this->assertTrue($definition->hasInjectionMethod('Foo', 'injectBar'));
        $this->assertContains('injectBar', $definition->getInjectionMethods('Foo'));
        $this->assertEquals(array('bar' => 'Bar'), $definition->getInjectionMethodParameters('Foo', 'injectBar'));
    }
    
}
