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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\CodeGenerator\Php;
use Zend\CodeGenerator\Php;
use Zend\Reflection;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class PhpClassTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $class = new Php\PhpClass();
        $this->isInstanceOf($class, '\Zend\CodeGenerator\Php\PhpClass');
    }

    public function testNameAccessors()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName('TestClass');
        $this->assertEquals($codeGenClass->getName(), 'TestClass');

    }

    public function testClassDocblockAccessors()
    {
        $this->markTestSkipped();
    }

    public function testAbstractAccessors()
    {
        $codeGenClass = new Php\PhpClass();
        $this->assertFalse($codeGenClass->isAbstract());
        $codeGenClass->setAbstract(true);
        $this->assertTrue($codeGenClass->isAbstract());
    }

    public function testExtendedClassAccessors()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setExtendedClass('ExtendedClass');
        $this->assertEquals($codeGenClass->getExtendedClass(), 'ExtendedClass');
    }

    public function testImplementedInterfacesAccessors()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setImplementedInterfaces(array('Class1', 'Class2'));
        $this->assertEquals($codeGenClass->getImplementedInterfaces(), array('Class1', 'Class2'));
    }

    public function testPropertyAccessors()
    {

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setProperties(array(
            array('name' => 'propOne'),
            new Php\PhpProperty(array('name' => 'propTwo'))
            ));

        $properties = $codeGenClass->getProperties();
        $this->assertEquals(count($properties), 2);
        $this->isInstanceOf(current($properties), 'Zend_CodeGenerator_Php_Property');

        $property = $codeGenClass->getProperty('propTwo');
        $this->isInstanceOf($property, 'Zend_CodeGenerator_Php_Property');
        $this->assertEquals($property->getName(), 'propTwo');

        // add a new property
        $codeGenClass->setProperty(array('name' => 'prop3'));
        $this->assertEquals(count($codeGenClass->getProperties()), 3);
    }

    public function testSetPropertyAlreadyExistsThrowsException()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setProperty(array('name' => 'prop3'));
        $this->setExpectedException('Zend\CodeGenerator\Php\Exception');

        $codeGenClass->setProperty(array('name' => 'prop3'));
    }

    public function testSetPropertyNoArrayOrPropertyThrowsException()
    {
        $this->setExpectedException('Zend\CodeGenerator\Php\Exception');

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setProperty("propertyName");
    }

    public function testMethodAccessors()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setMethods(array(
            array('name' => 'methodOne'),
            new Php\PhpMethod(array('name' => 'methodTwo'))
            ));

        $methods = $codeGenClass->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), '\Zend\CodeGenerator\Php\PhpMethod');

        $method = $codeGenClass->getMethod('methodOne');
        $this->isInstanceOf($method, '\Zend\CodeGenerator\Php\PhpMethod');
        $this->assertEquals($method->getName(), 'methodOne');

        // add a new property
        $codeGenClass->setMethod(array('name' => 'methodThree'));
        $this->assertEquals(count($codeGenClass->getMethods()), 3);
    }

    public function testSetMethodNoMethodOrArrayThrowsException()
    {
        $this->setExpectedException('Zend\CodeGenerator\Php\Exception',
            'setMethod() expects either an array of method options or an instance of Zend\CodeGenerator\Php\Method'
        );

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setMethod("aMethodName");
    }

    public function testSetMethodNameAlreadyExistsThrowsException()
    {
        $methodA = new Php\PhpMethod();
        $methodA->setName("foo");
        $methodB = new Php\PhpMethod();
        $methodB->setName("foo");

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setMethod($methodA);

        $this->setExpectedException('Zend\CodeGenerator\Php\Exception', 'A method by name foo already exists in this class.');

        $codeGenClass->setMethod($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $method = new Php\PhpMethod();
        $method->setName('methodOne');

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setMethod($method);

        $this->assertTrue($codeGenClass->hasMethod('methodOne'));
    }

    /**
     * @group ZF-7361
     */
    public function testHasProperty()
    {
        $property = new Php\PhpProperty();
        $property->setName('propertyOne');

        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setProperty($property);

        $this->assertTrue($codeGenClass->hasProperty('propertyOne'));
    }

    public function testToString()
    {
        $codeGenClass = new Php\PhpClass(array(
            'abstract' => true,
            'name' => 'SampleClass',
            'extendedClass' => 'ExtendedClassName',
            'implementedInterfaces' => array('Iterator', 'Traversable'),
            'properties' => array(
                array('name' => 'foo'),
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

        $output = $codeGenClass->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }

    /**
     * @group ZF-7909
     */
    public function testClassFromReflectionThatImplementsInterfaces()
    {
        $reflClass = new Reflection\ReflectionClass('ZendTest\CodeGenerator\Php\TestAsset\ClassWithInterface');

        $codeGen = Php\PhpClass::fromReflection($reflClass);
        $codeGen->setSourceDirty(true);

        $code = $codeGen->generate();
        
        $expectedClassDef = 'class ClassWithInterface implements OneInterface, TwoInterface';
        $this->assertContains($expectedClassDef, $code);
    }

    /**
     * @group ZF-7909
     */
    public function testClassFromReflectionDiscardParentImplementedInterfaces()
    {
        $reflClass = new Reflection\ReflectionClass('\ZendTest\CodeGenerator\Php\TestAsset\NewClassWithInterface');

        $codeGen = Php\PhpClass::fromReflection($reflClass);
        $codeGen->setSourceDirty(true);

        $code = $codeGen->generate();

        $expectedClassDef = 'class NewClassWithInterface extends ClassWithInterface implements ThreeInterface';
        $this->assertContains($expectedClassDef, $code);
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldIgnoreEmptyClassnameOnGenerate()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName( 'MyClass' )
                     ->setExtendedClass('');

        $expected = <<<CODE
class MyClass
{


}

CODE;
        $this->assertEquals( $expected, $codeGenClass->generate() );
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldNotIgnoreNonEmptyClassnameOnGenerate()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName( 'MyClass' )
                     ->setExtendedClass('ParentClass');

        $expected = <<<CODE
class MyClass extends ParentClass
{


}

CODE;
        $this->assertEquals( $expected, $codeGenClass->generate() );
    }

    /**
     * @group namespace
     */
    public function testCodeGenerationShouldTakeIntoAccountNamespacesFromReflection()
    {
        $reflClass = new Reflection\ReflectionClass('ZendTest\CodeGenerator\Php\TestAsset\ClassWithNamespace');
        $codeGen = Php\PhpClass::fromReflection($reflClass);
        $this->assertEquals('ZendTest\CodeGenerator\Php\TestAsset', $codeGen->getNamespaceName());
        $this->assertEquals('ClassWithNamespace', $codeGen->getName());
        $expected = <<<CODE
/** @namespace */
namespace ZendTest\CodeGenerator\Php\\TestAsset;

class ClassWithNamespace
{


}

CODE;
        $received = $codeGen->generate();
        $this->assertEquals($expected, $received, $received);
    }

    /**
     * @group namespace
     */
    public function testSetNameShouldDetermineIfNamespaceSegmentIsPresent()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName('My\Namespaced\FunClass');
        $this->assertEquals('My\Namespaced', $codeGenClass->getNamespaceName());
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedClassnameShouldGenerateANamespaceDeclaration()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName('My\Namespaced\FunClass');
        $received = $codeGenClass->generate();
        $this->assertContains('namespace My\Namespaced;', $received, $received);
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedClassnameShouldGenerateAClassnameWithoutItsNamespace()
    {
        $codeGenClass = new Php\PhpClass();
        $codeGenClass->setName('My\Namespaced\FunClass');
        $received = $codeGenClass->generate();
        $this->assertContains('class FunClass', $received, $received);
    }
}
