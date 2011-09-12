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
        $this->assertEquals(array('bar' => array('Bar', false, true)), $definition->getInjectionMethodParameters('Foo', 'injectBar'));
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
            array('username' => array(null, false, null), 'password' => array(null, false, null)),
            $definition->getInjectionMethodParameters('My\DbAdapter', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Mapper'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Mapper'));
        $this->assertEquals(
            array('dbAdapter' => array('My\DbAdapter', false, true)),
            $definition->getInjectionMethodParameters('My\Mapper', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Repository'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Repository'));
        $this->assertEquals(
            array('mapper' => array('My\Mapper', false, true)),
            $definition->getInjectionMethodParameters('My\Repository', '__construct')
            );
        
    }

    public function testCanCreateClassFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $class = $builder->createClass('Foo');

        $this->assertTrue($builder->hasClass('Foo'));
    }
    
    public function testCanCreateInjectionMethodsAndPopulateFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $foo     = $builder->createClass('Foo');
        $foo->setName('Foo');
        $foo->createInjectionMethod('setBar')
            ->addParameter('bar', 'Bar');
        $foo->createInjectionMethod('setConfig')
            ->addParameter('config', null);

        $this->assertTrue($builder->hasClass('Foo'));
        $this->assertTrue($builder->hasInjectionMethod('Foo', 'setBar'));
        $this->assertTrue($builder->hasInjectionMethod('Foo', 'setConfig'));

        $this->assertEquals(array('bar' => array('Bar', false, true)), $builder->getInjectionMethodParameters('Foo', 'setBar'));
        $this->assertEquals(array('config' => array(null, false, null)), $builder->getInjectionMethodParameters('Foo', 'setConfig'));
    }

    public function testBuilderCanSpecifyClassToUseWithCreateClass()
    {
        $builder = new BuilderDefinition();
        $this->assertEquals('Zend\Di\Definition\Builder\PhpClass', $builder->getClassBuilder());

        $builder->setClassBuilder('Foo');
        $this->assertEquals('Foo', $builder->getClassBuilder());
    }

    public function testClassBuilderCanSpecifyClassToUseWhenCreatingInjectionMethods()
    {
        $builder = new BuilderDefinition();
        $class   = $builder->createClass('Foo');

        $this->assertEquals('Zend\Di\Definition\Builder\InjectionMethod', $class->getMethodBuilder());

        $class->setMethodBuilder('Foo');
        $this->assertEquals('Foo', $class->getMethodBuilder());
    }
}
