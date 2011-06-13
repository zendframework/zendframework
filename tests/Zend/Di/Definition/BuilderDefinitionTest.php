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
    
    public function testBuilderCanBuildFromArray()
    {
        $ini = new \Zend\Config\Ini(__DIR__ . '/../_files/sample.ini', 'section-b');
        $iniAsArray = $ini->toArray();
        $definitionArray = $iniAsArray['di']['definitions'][1];
        unset($definitionArray['class']);
        
        $definition = new BuilderDefinition();
        $definition->createClassesFromArray($definitionArray);
        
        $this->assertTrue($definition->hasClass('My\DbAdapter'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\DbAdapter'));
        $this->assertEquals(
            array('username' => null, 'password' => null),
            $definition->getInjectionMethodParameters('My\DbAdapter', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Mapper'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Mapper'));
        $this->assertEquals(
            array('dbAdapter' => 'My\DbAdapter'),
            $definition->getInjectionMethodParameters('My\Mapper', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Repository'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Repository'));
        $this->assertEquals(
            array('mapper' => 'My\Mapper'),
            $definition->getInjectionMethodParameters('My\Repository', '__construct')
            );
        
    }
    
}
