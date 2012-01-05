<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Code\Generator;

use Zend\Code\Generator\ClassGenerator,
    Zend\Code\Generator\PropertyGenerator,
    Zend\Code\Generator\MethodGenerator,
    Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class ClassGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $class = new ClassGenerator();
        $this->isInstanceOf($class, '\Zend\Code\Generator\ClassGenerator');
    }

    public function testNameAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('TestClass');
        $this->assertEquals($classGenerator->getName(), 'TestClass');

    }

    public function testClassDocblockAccessors()
    {
        $this->markTestSkipped();
    }

    public function testAbstractAccessors()
    {
        $classGenerator = new ClassGenerator();
        $this->assertFalse($classGenerator->isAbstract());
        $classGenerator->setAbstract(true);
        $this->assertTrue($classGenerator->isAbstract());
    }

    public function testExtendedClassAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setExtendedClass('ExtendedClass');
        $this->assertEquals($classGenerator->getExtendedClass(), 'ExtendedClass');
    }

    public function testImplementedInterfacesAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setImplementedInterfaces(array('Class1', 'Class2'));
        $this->assertEquals($classGenerator->getImplementedInterfaces(), array('Class1', 'Class2'));
    }

    public function testPropertyAccessors()
    {

        $classGenerator = new ClassGenerator();
        $classGenerator->setProperties(array(
            'propOne',
            new PropertyGenerator('propTwo')
            ));

        $properties = $classGenerator->getProperties();
        $this->assertEquals(count($properties), 2);
        $this->assertInstanceOf('PropertyGenerator', current($properties));

        $property = $classGenerator->getProperty('propTwo');
        $this->assertInstanceOf('PropertyGenerator', $property);
        $this->assertEquals($property->getName(), 'propTwo');

        // add a new property
        $classGenerator->setProperty('prop3');
        $this->assertEquals(count($classGenerator->getProperties()), 3);
    }

    public function testSetPropertyAlreadyExistsThrowsException()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setProperty('prop3');

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'A property by name prop3 already exists in this class'
            );
        $classGenerator->setProperty('prop3');
    }

    public function testSetPropertyNoArrayOrPropertyThrowsException()
    {
        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'setProperty() expects either a string or an instance of Zend\Code\Generator\PropertyGenerator'
            );
        $classGenerator->setProperty(true);
    }

    public function testMethodAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setMethods(array(
            'methodOne',
            new MethodGenerator('methodTwo')
            ));

        $methods = $classGenerator->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), '\Zend\Code\Generator\PhpMethod');

        $method = $classGenerator->getMethod('methodOne');
        $this->isInstanceOf($method, '\Zend\Code\Generator\PhpMethod');
        $this->assertEquals($method->getName(), 'methodOne');

        // add a new property
        $classGenerator->setMethod('methodThree');
        $this->assertEquals(count($classGenerator->getMethods()), 3);
    }

    public function testSetMethodNoMethodOrArrayThrowsException()
    {
        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception',
            'setMethod() expects either a string method name or an instance of Zend\Code\Generator\MethodGenerator'
            );

        $classGenerator->setMethod(true);
    }

    public function testSetMethodNameAlreadyExistsThrowsException()
    {
        $methodA = new MethodGenerator();
        $methodA->setName("foo");
        $methodB = new MethodGenerator();
        $methodB->setName("foo");

        $classGenerator = new ClassGenerator();
        $classGenerator->setMethod($methodA);

        $this->setExpectedException('Zend\Code\Generator\Exception\InvalidArgumentException', 'A method by name foo already exists in this class.');

        $classGenerator->setMethod($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $method = new MethodGenerator();
        $method->setName('methodOne');

        $classGenerator = new ClassGenerator();
        $classGenerator->setMethod($method);

        $this->assertTrue($classGenerator->hasMethod('methodOne'));
    }

    /**
     * @group ZF-7361
     */
    public function testHasProperty()
    {
        $property = new PropertyGenerator();
        $property->setName('propertyOne');

        $classGenerator = new ClassGenerator();
        $classGenerator->setProperty($property);

        $this->assertTrue($classGenerator->hasProperty('propertyOne'));
    }

    public function testToString()
    {
        $classGenerator = new ClassGenerator(
            'SampleClass',


            array(
            'abstract' => true,
            'name' => 'SampleClass',
            'extendedClass' => 'ExtendedClassName',
            'implementedInterfaces' => array('Iterator', 'Traversable'),
            'properties' => array('foo',
                array('name' => 'bar')
                ),
            'methods' => array(
                array('name' => 'baz')
                ),
            ));

        $expectedOutput = <<<EOS
abstract class SampleClass extends ExtendedClassName implements Iterator, Traversable
{

    public \$foo = null;

    public \$bar = null;

    public function baz()
    {
    }


}

EOS;

        $output = $classGenerator->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }

    /**
     * @group ZF-7909
     */
    public function testClassFromReflectionThatImplementsInterfaces()
    {
        $reflClass = new ClassReflection('ZendTest\Code\Generator\TestAsset\ClassWithInterface');

        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $classGenerator->setSourceDirty(true);

        $code = $classGenerator->generate();

        $expectedClassDef = 'class ClassWithInterface'
                          . ' implements ZendTest\Code\Generator\TestAsset\OneInterface'
                          . ', ZendTest\Code\Generator\TestAsset\TwoInterface';
        $this->assertContains($expectedClassDef, $code);
    }

    /**
     * @group ZF-7909
     */
    public function testClassFromReflectionDiscardParentImplementedInterfaces()
    {
        $reflClass = new ClassReflection('\ZendTest\Code\Generator\TestAsset\NewClassWithInterface');

        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $classGenerator->setSourceDirty(true);

        $code = $classGenerator->generate();

        $expectedClassDef = 'class NewClassWithInterface'
                          . ' extends ZendTest\Code\Generator\TestAsset\ClassWithInterface'
                          . ' implements ZendTest\Code\Generator\TestAsset\ThreeInterface';
        $this->assertContains($expectedClassDef, $code);
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldIgnoreEmptyClassnameOnGenerate()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass->setName( 'MyClass' )
                     ->setExtendedClass('');

        $expected = <<<CODE
class MyClass
{


}

CODE;
        $this->assertEquals( $expected, $classGeneratorClass->generate() );
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldNotIgnoreNonEmptyClassnameOnGenerate()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass->setName( 'MyClass' )
                     ->setExtendedClass('ParentClass');

        $expected = <<<CODE
class MyClass extends ParentClass
{


}

CODE;
        $this->assertEquals( $expected, $classGeneratorClass->generate() );
    }

    /**
     * @group namespace
     */
    public function testCodeGenerationShouldTakeIntoAccountNamespacesFromReflection()
    {
        $reflClass = new ClassReflection('ZendTest\Code\Generator\TestAsset\ClassWithNamespace');
        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $this->assertEquals('ZendTest\Code\Generator\TestAsset', $classGenerator->getNamespaceName());
        $this->assertEquals('ClassWithNamespace', $classGenerator->getName());
        $expected = <<<CODE
/** @namespace */
namespace ZendTest\Code\Generator\\TestAsset;

class ClassWithNamespace
{


}

CODE;
        $received = $classGenerator->generate();
        $this->assertEquals($expected, $received, $received);
    }

    /**
     * @group namespace
     */
    public function testSetNameShouldDetermineIfNamespaceSegmentIsPresent()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass->setName('My\Namespaced\FunClass');
        $this->assertEquals('My\Namespaced', $classGeneratorClass->getNamespaceName());
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedClassnameShouldGenerateANamespaceDeclaration()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass->setName('My\Namespaced\FunClass');
        $received = $classGeneratorClass->generate();
        $this->assertContains('namespace My\Namespaced;', $received, $received);
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedClassnameShouldGenerateAClassnameWithoutItsNamespace()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass->setName('My\Namespaced\FunClass');
        $received = $classGeneratorClass->generate();
        $this->assertContains('class FunClass', $received, $received);
    }
}
