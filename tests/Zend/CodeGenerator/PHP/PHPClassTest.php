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
namespace ZendTest\CodeGenerator\PHP;
use Zend\CodeGenerator\PHP;
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
class PHPClassTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $class = new PHP\PHPClass();
        $this->isInstanceOf($class, '\Zend\CodeGenerator\PHP\PHPClass');
    }

    public function testNameAccessors()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setName('TestClass');
        $this->assertEquals($codeGenClass->getName(), 'TestClass');

    }

    public function testClassDocblockAccessors()
    {
        $this->markTestSkipped();
    }

    public function testAbstractAccessors()
    {
        $codeGenClass = new PHP\PHPClass();
        $this->assertFalse($codeGenClass->isAbstract());
        $codeGenClass->setAbstract(true);
        $this->assertTrue($codeGenClass->isAbstract());
    }

    public function testExtendedClassAccessors()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setExtendedClass('ExtendedClass');
        $this->assertEquals($codeGenClass->getExtendedClass(), 'ExtendedClass');
    }

    public function testImplementedInterfacesAccessors()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setImplementedInterfaces(array('Class1', 'Class2'));
        $this->assertEquals($codeGenClass->getImplementedInterfaces(), array('Class1', 'Class2'));
    }

    public function testPropertyAccessors()
    {

        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setProperties(array(
            array('name' => 'propOne'),
            new PHP\PHPProperty(array('name' => 'propTwo'))
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

    /**
     * @expectedException Zend\CodeGenerator\PHP\Exception
     */
    public function testSetPropertyAlreadyExistsThrowsException()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setProperty(array('name' => 'prop3'));
        $codeGenClass->setProperty(array('name' => 'prop3'));
    }

    /**
     * @expectedException Zend\CodeGenerator\PHP\Exception
     */
    public function testSetPropertyNoArrayOrPropertyThrowsException()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setProperty("propertyName");
    }

    public function testMethodAccessors()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setMethods(array(
            array('name' => 'methodOne'),
            new PHP\PHPMethod(array('name' => 'methodTwo'))
            ));

        $methods = $codeGenClass->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), '\Zend\CodeGenerator\PHP\PHPMethod');

        $method = $codeGenClass->getMethod('methodOne');
        $this->isInstanceOf($method, '\Zend\CodeGenerator\PHP\PHPMethod');
        $this->assertEquals($method->getName(), 'methodOne');

        // add a new property
        $codeGenClass->setMethod(array('name' => 'methodThree'));
        $this->assertEquals(count($codeGenClass->getMethods()), 3);
    }

    /**
     * @expectedException Zend\CodeGenerator\PHP\Exception
     */
    public function testSetMethodNoMethodOrArrayThrowsException()
    {
        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setMethod("aMethodName");
    }

    /**
     * @expectedException Zend\CodeGenerator\PHP\Exception
     */
    public function testSetMethodNameAlreadyExistsThrowsException()
    {
        $methodA = new PHP\PHPMethod();
        $methodA->setName("foo");
        $methodB = new PHP\PHPMethod();
        $methodB->setName("foo");

        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setMethod($methodA);

        $codeGenClass->setMethod($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $method = new PHP\PHPMethod();
        $method->setName('methodOne');

        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setMethod($method);

        $this->assertTrue($codeGenClass->hasMethod('methodOne'));
    }

    /**
     * @group ZF-7361
     */
    public function testHasProperty()
    {
        $property = new PHP\PHPProperty();
        $property->setName('propertyOne');

        $codeGenClass = new PHP\PHPClass();
        $codeGenClass->setProperty($property);

        $this->assertTrue($codeGenClass->hasProperty('propertyOne'));
    }

    public function testToString()
    {
        $codeGenClass = new PHP\PHPClass(array(
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
        $reflClass = new Reflection\ReflectionClass('\ZendTest\CodeGenerator\PHP\TestAsset\ClassWithInterface');

        $codeGen = PHP\PHPClass::fromReflection($reflClass);
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
        $reflClass = new Reflection\ReflectionClass('\ZendTest\CodeGenerator\PHP\TestAsset\NewClassWithInterface');

        $codeGen = PHP\PHPClass::fromReflection($reflClass);
        $codeGen->setSourceDirty(true);

        $code = $codeGen->generate();

        $expectedClassDef = 'class NewClassWithInterface extends ClassWithInterface implements ThreeInterface';
        $this->assertContains($expectedClassDef, $code);
    }
}
