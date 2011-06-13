<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\ArrayDefinition,
    PHPUnit_Framework_TestCase as TestCase;

class ArrayDefinitionTest extends TestCase
{
    
    /**
     * @var Zend\Di\Definition\ArrayDefinition
     */
    protected $definition = null;
    
    public function setup()
    {
        $this->definition = new ArrayDefinition(include __DIR__ . '/../_files/definition-array.php');
    }
    
    public function testArrayDefinitionHasClasses()
    {
        $this->assertTrue($this->definition->hasClass('My\DbAdapter'));
        $this->assertTrue($this->definition->hasClass('My\EntityA'));
        $this->assertTrue($this->definition->hasClass('My\Mapper'));
        $this->assertTrue($this->definition->hasClass('My\RepositoryA'));
        $this->assertTrue($this->definition->hasClass('My\RepositoryB'));
        $this->assertFalse($this->definition->hasClass('My\Foo'));
    }
    
    public function testArrayDefinitionCanGetClassses()
    {
        $list = array(
            'My\DbAdapter',
            'My\EntityA',
            'My\Mapper',
            'My\RepositoryA',
            'My\RepositoryB'
        );
        
        $classes = $this->definition->getClasses();
        
        foreach ($list as $class) {
            $this->assertContains($class, $classes);
        }
        
    }
    
    public function testArrayDefinitionCanGetClassSupertypes()
    {
        $this->assertEquals(array(), $this->definition->getClassSupertypes('My\EntityA'));
        $this->assertContains('My\RepositoryA', $this->definition->getClassSupertypes('My\RepositoryB'));
    }
    
    
    public function testArrayDefinitionCanGetInstantiator()
    {
        $this->assertEquals('__construct', $this->definition->getInstantiator('My\RepositoryA'));
        $this->assertNull($this->definition->getInstantiator('My\Foo'));
    }
    
    public function testArrayDefinitionHasInjectionMethods()
    {
        $this->markTestIncomplete();
    }
    
    public function testArrayDefinitionHasInjectionMethod()
    {
        $this->markTestIncomplete();
    }
    
    public function testArrayDefinitionGetInjectionMethods()
    {
        $this->markTestIncomplete();
    }

    public function testArrayDefinitionGetInjectionMethodParameters()
    {
        $this->markTestIncomplete();
    }

    
    
}
