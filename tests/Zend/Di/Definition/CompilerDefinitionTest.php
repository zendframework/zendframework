<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\CompilerDefinition,
    Zend\Code\Scanner\DirectoryScanner,
    PHPUnit_Framework_TestCase as TestCase;

class CompilerDefinitionTest extends TestCase
{
    public function testCompilerCompilesAgainstConstructorInjectionAssets()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/CompilerClasses');
        $definition->compile();

        $this->assertTrue($definition->hasClass('ZendTest\Di\TestAsset\CompilerClasses\A'));
        
        $assertClasses = array(
            'ZendTest\Di\TestAsset\CompilerClasses\A',
            'ZendTest\Di\TestAsset\CompilerClasses\B',
            'ZendTest\Di\TestAsset\CompilerClasses\C',
            'ZendTest\Di\TestAsset\CompilerClasses\D',
        );
        $classes = $definition->getClasses();
        foreach ($assertClasses as $assertClass) {
            $this->assertContains($assertClass, $classes);
        }

        // @todo this needs to be resolved, not the short name
        // $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        
        $this->assertEquals('__construct', $definition->getInstantiator('ZendTest\Di\TestAsset\CompilerClasses\A'));
        $this->assertTrue($definition->hasMethods('ZendTest\Di\TestAsset\CompilerClasses\C'));
        

        $this->assertArrayHasKey('setB', $definition->getMethods('ZendTest\Di\TestAsset\CompilerClasses\C'));
        $this->assertTrue($definition->hasMethod('ZendTest\Di\TestAsset\CompilerClasses\C', 'setB'));
        
        $this->assertEquals(
            array('ZendTest\Di\TestAsset\CompilerClasses\C::setB:0' => array('b', 'ZendTest\Di\TestAsset\CompilerClasses\B', true)),
            $definition->getMethodParameters('ZendTest\Di\TestAsset\CompilerClasses\C', 'setB')
        );
    }

    public function testCompilerSupertypes()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/CompilerClasses');
        $definition->compile();
        $this->assertCount(0, $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\C'));
        $this->assertCount(1, $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertCount(2, $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
    }
}
