<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\CompilerDefinition;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\Code\Scanner\FileScanner;
use PHPUnit_Framework_TestCase as TestCase;

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
            array('ZendTest\Di\TestAsset\CompilerClasses\C::setB:0' => array('b', 'ZendTest\Di\TestAsset\CompilerClasses\B', true, null)),
            $definition->getMethodParameters('ZendTest\Di\TestAsset\CompilerClasses\C', 'setB')
        );
    }

    public function testCompilerSupertypes()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/CompilerClasses');
        $definition->compile();
        $this->assertEquals(0, count($definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\C')));
        $this->assertEquals(1, count($definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D')));
        $this->assertEquals(2, count($definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E')));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerDirectoryScannerAndFileScanner()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectoryScanner(new DirectoryScanner(__DIR__ . '/../TestAsset/CompilerClasses'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/A.php'));
        $definition->compile();
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerFileScanner()
    {
        $definition = new CompilerDefinition;
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/C.php'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/D.php'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/E.php'));
        $definition->compile();
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerReflectionException()
    {
        $this->setExpectedException('ReflectionException', 'Class ZendTest\Di\TestAsset\InvalidCompilerClasses\Foo does not exist');
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/InvalidCompilerClasses');
        $definition->compile();
    }

    public function testCompilerAllowReflectionException()
    {
        $definition = new CompilerDefinition;
        $definition->setAllowReflectionExceptions();
        $definition->addDirectory(__DIR__ . '/../TestAsset/InvalidCompilerClasses');
        $definition->compile();
        $parameters = $definition->getMethodParameters('ZendTest\Di\TestAsset\InvalidCompilerClasses\InvalidClass', '__construct');

        // The exception gets caught before the parameter's class is set
        $this->assertCount(1, current($parameters));
    }

    /**
     * @group ZF2-308
     */
    public function testStaticMethodsNotIncludedInDefinitions()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/SetterInjection');
        $definition->compile();
        $this->assertTrue($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setFoo'));
        $this->assertFalse($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setName'));
    }
}
