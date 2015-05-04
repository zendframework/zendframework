<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use ReflectionMethod;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Reflection\ClassReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $class = new ClassGenerator();
        $this->isInstanceOf($class, 'Zend\Code\Generator\ClassGenerator');
    }

    public function testNameAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('TestClass');
        $this->assertEquals($classGenerator->getName(), 'TestClass');
    }

    public function testClassDocBlockAccessors()
    {
        $docBlockGenerator = new DocBlockGenerator();
        $classGenerator = new ClassGenerator();
        $classGenerator->setDocBlock($docBlockGenerator);
        $this->assertSame($docBlockGenerator, $classGenerator->getDocBlock());
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
        $classGenerator->addProperties(array(
            'propOne',
            new PropertyGenerator('propTwo')
        ));

        $properties = $classGenerator->getProperties();
        $this->assertEquals(count($properties), 2);
        $this->assertInstanceOf('Zend\Code\Generator\PropertyGenerator', current($properties));

        $property = $classGenerator->getProperty('propTwo');
        $this->assertInstanceOf('Zend\Code\Generator\PropertyGenerator', $property);
        $this->assertEquals($property->getName(), 'propTwo');

        // add a new property
        $classGenerator->addProperty('prop3');
        $this->assertEquals(count($classGenerator->getProperties()), 3);
    }

    public function testSetPropertyAlreadyExistsThrowsException()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addProperty('prop3');

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'A property by name prop3 already exists in this class'
        );
        $classGenerator->addProperty('prop3');
    }

    public function testSetPropertyNoArrayOrPropertyThrowsException()
    {
        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Zend\Code\Generator\ClassGenerator::addProperty expects string for name'
        );
        $classGenerator->addProperty(true);
    }

    public function testMethodAccessors()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addMethods(array(
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
        $classGenerator->addMethod('methodThree');
        $this->assertEquals(count($classGenerator->getMethods()), 3);
    }

    public function testSetMethodNoMethodOrArrayThrowsException()
    {
        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\ExceptionInterface',
            'Zend\Code\Generator\ClassGenerator::addMethod expects string for name'
        );

        $classGenerator->addMethod(true);
    }

    public function testSetMethodNameAlreadyExistsThrowsException()
    {
        $methodA = new MethodGenerator();
        $methodA->setName("foo");
        $methodB = new MethodGenerator();
        $methodB->setName("foo");

        $classGenerator = new ClassGenerator();
        $classGenerator->addMethodFromGenerator($methodA);

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'A method by name foo already exists in this class.'
        );

        $classGenerator->addMethodFromGenerator($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addMethod('methodOne');

        $this->assertTrue($classGenerator->hasMethod('methodOne'));
    }

    public function testRemoveMethod()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addMethod('methodOne');
        $this->assertTrue($classGenerator->hasMethod('methodOne'));

        $classGenerator->removeMethod('methodOne');
        $this->assertFalse($classGenerator->hasMethod('methodOne'));
    }

    /**
     * @group ZF-7361
     */
    public function testHasProperty()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addProperty('propertyOne');

        $this->assertTrue($classGenerator->hasProperty('propertyOne'));
    }

    public function testToString()
    {
        $classGenerator = ClassGenerator::fromArray(array(
            'name' => 'SampleClass',
            'flags' => ClassGenerator::FLAG_ABSTRACT,
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
        $reflClass = new ClassReflection('ZendTest\Code\Generator\TestAsset\NewClassWithInterface');

        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $classGenerator->setSourceDirty(true);

        $code = $classGenerator->generate();

        $expectedClassDef = 'class NewClassWithInterface'
            . ' extends ZendTest\Code\Generator\TestAsset\ClassWithInterface'
            . ' implements ZendTest\Code\Generator\TestAsset\ThreeInterface';
        $this->assertContains($expectedClassDef, $code);
    }

    /**
     * @group 4988
     */
    public function testNonNamespaceClassReturnsAllMethods()
    {
        require_once __DIR__ . '/../TestAsset/NonNamespaceClass.php';

        $reflClass = new ClassReflection('ZendTest_Code_NsTest_BarClass');
        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $this->assertCount(1, $classGenerator->getMethods());
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldIgnoreEmptyClassnameOnGenerate()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass
            ->setName('MyClass')
            ->setExtendedClass('');

        $expected = <<<CODE
class MyClass
{


}

CODE;
        $this->assertEquals($expected, $classGeneratorClass->generate());
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldNotIgnoreNonEmptyClassnameOnGenerate()
    {
        $classGeneratorClass = new ClassGenerator();
        $classGeneratorClass
            ->setName('MyClass')
            ->setExtendedClass('ParentClass');

        $expected = <<<CODE
class MyClass extends ParentClass
{


}

CODE;
        $this->assertEquals($expected, $classGeneratorClass->generate());
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

    /**
     * @group ZF2-151
     */
    public function testAddUses()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('My\Class');
        $classGenerator->addUse('My\First\Use\Class');
        $classGenerator->addUse('My\Second\Use\Class', 'MyAlias');
        $generated = $classGenerator->generate();

        $this->assertContains('use My\First\Use\Class;', $generated);
        $this->assertContains('use My\Second\Use\Class as MyAlias;', $generated);
    }

    /**
     * @group 4990
     */
    public function testAddOneUseTwiceOnlyAddsOne()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('My\Class');
        $classGenerator->addUse('My\First\Use\Class');
        $classGenerator->addUse('My\First\Use\Class');
        $generated = $classGenerator->generate();

        $this->assertCount(1, $classGenerator->getUses());

        $this->assertContains('use My\First\Use\Class;', $generated);
    }

    /**
     * @group 4990
     */
    public function testAddOneUseWithAliasTwiceOnlyAddsOne()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('My\Class');
        $classGenerator->addUse('My\First\Use\Class', 'MyAlias');
        $classGenerator->addUse('My\First\Use\Class', 'MyAlias');
        $generated = $classGenerator->generate();

        $this->assertCount(1, $classGenerator->getUses());

        $this->assertContains('use My\First\Use\Class as MyAlias;', $generated);
    }

    public function testCreateFromArrayWithDocBlockFromArray()
    {
        $classGenerator = ClassGenerator::fromArray(array(
            'name' => 'SampleClass',
            'docblock' => array(
                'shortdescription' => 'foo',
            ),
        ));

        $docBlock = $classGenerator->getDocBlock();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $docBlock);
    }

    public function testCreateFromArrayWithDocBlockInstance()
    {
        $classGenerator = ClassGenerator::fromArray(array(
            'name' => 'SampleClass',
            'docblock' => new DocBlockGenerator('foo'),
        ));

        $docBlock = $classGenerator->getDocBlock();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $docBlock);
    }

    public function testExtendedClassProperies()
    {
        $reflClass = new ClassReflection('ZendTest\Code\Generator\TestAsset\ExtendedClassWithProperties');
        $classGenerator = ClassGenerator::fromReflection($reflClass);
        $code = $classGenerator->generate();
        $this->assertContains('publicExtendedClassProperty', $code);
        $this->assertContains('protectedExtendedClassProperty', $code);
        $this->assertContains('privateExtendedClassProperty', $code);
        $this->assertNotContains('publicClassProperty', $code);
        $this->assertNotContains('protectedClassProperty', $code);
        $this->assertNotContains('privateClassProperty', $code);
    }

    public function testHasMethodInsensitive()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addMethod('methodOne');

        $this->assertTrue($classGenerator->hasMethod('methodOne'));
        $this->assertTrue($classGenerator->hasMethod('MethoDonE'));
    }

    public function testRemoveMethodInsensitive()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->addMethod('methodOne');

        $classGenerator->removeMethod('METHODONe');
        $this->assertFalse($classGenerator->hasMethod('methodOne'));
    }

    public function testGenerateClassAndAddMethod()
    {
        $classGenerator = new ClassGenerator();
        $classGenerator->setName('MyClass');
        $classGenerator->addMethod('methodOne');

        $expected = <<<CODE
class MyClass
{

    public function methodOne()
    {
    }


}

CODE;

        $output = $classGenerator->generate();
        $this->assertEquals($expected, $output);
    }

    /**
     * @group 6274
     */
    public function testCanAddConstant()
    {
        $classGenerator = new ClassGenerator();

        $classGenerator->setName('My\Class');
        $classGenerator->addConstant('x', 'value');

        $this->assertTrue($classGenerator->hasConstant('x'));

        $constant = $classGenerator->getConstant('x');

        $this->assertInstanceOf('Zend\Code\Generator\PropertyGenerator', $constant);
        $this->assertTrue($constant->isConst());
        $this->assertEquals($constant->getDefaultValue()->getValue(), 'value');
    }

    /**
     * @group 6274
     */
    public function testCanAddConstantsWithArrayOfGenerators()
    {
        $classGenerator = new ClassGenerator();

        $classGenerator->addConstants(array(
            new PropertyGenerator('x', 'value1', PropertyGenerator::FLAG_CONSTANT),
            new PropertyGenerator('y', 'value2', PropertyGenerator::FLAG_CONSTANT)
        ));

        $this->assertCount(2, $classGenerator->getConstants());
        $this->assertEquals($classGenerator->getConstant('x')->getDefaultValue()->getValue(), 'value1');
        $this->assertEquals($classGenerator->getConstant('y')->getDefaultValue()->getValue(), 'value2');
    }

    /**
     * @group 6274
     */
    public function testCanAddConstantsWithArrayOfKeyValues()
    {
        $classGenerator = new ClassGenerator();

        $classGenerator->addConstants(array(
            array( 'name'=> 'x', 'value' => 'value1'),
            array('name' => 'y', 'value' => 'value2')
        ));

        $this->assertCount(2, $classGenerator->getConstants());
        $this->assertEquals($classGenerator->getConstant('x')->getDefaultValue()->getValue(), 'value1');
        $this->assertEquals($classGenerator->getConstant('y')->getDefaultValue()->getValue(), 'value2');
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionWithInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $classGenerator = new ClassGenerator();

        $classGenerator->addConstant(array(), 'value1');
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionWithInvalidValue()
    {
        $this->setExpectedException('InvalidArgumentException');

        $classGenerator = new ClassGenerator();

        $classGenerator->addConstant('x', null);
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionOnDuplicate()
    {
        $this->setExpectedException('InvalidArgumentException');

        $classGenerator = new ClassGenerator();

        $classGenerator->addConstant('x', 'value1');
        $classGenerator->addConstant('x', 'value1');
    }

    /**
     * @group 6274
     */
    public function testAddPropertyIsBackwardsCompatibleWithConstants()
    {
        $classGenerator = new ClassGenerator();

        $classGenerator->addProperty('x', 'value1', PropertyGenerator::FLAG_CONSTANT);

        $this->assertEquals($classGenerator->getConstant('x')->getDefaultValue()->getValue(), 'value1');
    }

    /**
     * @group 6274
     */
    public function testAddPropertiesIsBackwardsCompatibleWithConstants()
    {
        $constants = array(
            new PropertyGenerator('x', 'value1', PropertyGenerator::FLAG_CONSTANT),
            new PropertyGenerator('y', 'value2', PropertyGenerator::FLAG_CONSTANT)
        );
        $classGenerator = new ClassGenerator();

        $classGenerator->addProperties($constants);

        $this->assertCount(2, $classGenerator->getConstants());
        $this->assertEquals($classGenerator->getConstant('x')->getDefaultValue()->getValue(), 'value1');
        $this->assertEquals($classGenerator->getConstant('y')->getDefaultValue()->getValue(), 'value2');
    }

    /**
     * @group 6274
     */
    public function testConstantsAddedFromReflection()
    {
        $reflector      = new ClassReflection('ZendTest\Code\Generator\TestAsset\TestClassWithManyProperties');
        $classGenerator = ClassGenerator::fromReflection($reflector);
        $constant       = $classGenerator->getConstant('FOO');

        $this->assertEquals($constant->getDefaultValue()->getValue(), 'foo');
    }

    /**
     * @group 6274
     */
    public function testClassCanBeGeneratedWithConstantAndPropertyWithSameName()
    {
        $reflector      = new ClassReflection('ZendTest\Code\Generator\TestAsset\TestSampleSingleClass');
        $classGenerator = ClassGenerator::fromReflection($reflector);

        $classGenerator->addProperty('fooProperty', true, PropertyGenerator::FLAG_PUBLIC);
        $classGenerator->addConstant('fooProperty', 'duplicate');

        $contents = <<<'CODE'
namespace ZendTest\Code\Generator\TestAsset;

/**
 * class docblock
 */
class TestSampleSingleClass
{

    const fooProperty = 'duplicate';

    public $fooProperty = true;

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function someMethod()
    {
        /* test test */
    }


}

CODE;

        $this->assertEquals($classGenerator->generate(), $contents);
    }
    /**
     * @group 6253
     */
    public function testHereDoc()
    {
        $reflector = new ClassReflection('ZendTest\Code\Generator\TestAsset\TestClassWithHeredoc');
        $classGenerator = new ClassGenerator();
        $methods = $reflector->getMethods();
        $classGenerator->setName("OutputClass");

        foreach ($methods as $method) {
            $methodGenerator = MethodGenerator::fromReflection($method);

            $classGenerator->addMethodFromGenerator($methodGenerator);
        }

        $contents = <<< 'CODE'
class OutputClass
{

    public function someFunction()
    {
        $output = <<< END

                Fix it, fix it!
                Fix it, fix it!
                Fix it, fix it!
END;
    }


}

CODE;

        $this->assertEquals($contents, $classGenerator->generate());
    }

    public function testCanAddTraitWithString()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTrait('myTrait');
        $this->assertTrue($classGenerator->hasTrait('myTrait'));
    }

    public function testCanAddTraitWithArray()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTrait(array('traitName' => 'myTrait'));
        $this->assertTrue($classGenerator->hasTrait('myTrait'));
    }

    public function testCanRemoveTrait()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTrait(array('traitName' => 'myTrait'));
        $this->assertTrue($classGenerator->hasTrait('myTrait'));
        $classGenerator->removeTrait('myTrait');
        $this->assertFalse($classGenerator->hasTrait('myTrait'));
    }

    public function testCanGetTraitsMethod()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTraits(array('myTrait', 'hisTrait'));

        $traits = $classGenerator->getTraits();
        $this->assertContains('myTrait', $traits);
        $this->assertContains('hisTrait', $traits);
    }

    public function testCanAddTraitAliasWithString()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('myTrait::method', 'useMe', ReflectionMethod::IS_PRIVATE);

        $aliases = $classGenerator->getTraitAliases();
        $this->assertArrayHasKey('myTrait::method', $aliases);
        $this->assertEquals($aliases['myTrait::method']['alias'], 'useMe');
        $this->assertEquals($aliases['myTrait::method']['visibility'], ReflectionMethod::IS_PRIVATE);
    }

    public function testCanAddTraitAliasWithArray()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias(array(
            'traitName' => 'myTrait',
            'method'    => 'method',
        ), 'useMe', ReflectionMethod::IS_PRIVATE);

        $aliases = $classGenerator->getTraitAliases();
        $this->assertArrayHasKey('myTrait::method', $aliases);
        $this->assertEquals($aliases['myTrait::method']['alias'], 'useMe');
        $this->assertEquals($aliases['myTrait::method']['visibility'], ReflectionMethod::IS_PRIVATE);
    }

    public function testAddTraitAliasExceptionInvalidMethodFormat()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Format: $method must be in the format of trait::method'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('method', 'useMe');
    }

    public function testAddTraitAliasExceptionInvalidMethodTraitDoesNotExist()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid trait: Trait does not exists on this class'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('unknown::method', 'useMe');
    }

    public function testAddTraitAliasExceptionMethodAlreadyExists()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Alias: Method name already exists on this class.'
        );

        $classGenerator->addMethod('methodOne');
        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('myTrait::method', 'methodOne');
    }

    public function testAddTraitAliasExceptionInvalidVisibilityValue()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Type: $visibility must of ReflectionMethod::IS_PUBLIC,'
            . ' ReflectionMethod::IS_PRIVATE or ReflectionMethod::IS_PROTECTED'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('myTrait::method', 'methodOne', 'public');
    }

    public function testAddTraitAliasExceptionInvalidAliasArgument()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Alias: $alias must be a string or array.'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitAlias('myTrait::method', new ClassGenerator, 'public');
    }

    public function testCanAddTraitOverride()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTraits(array('myTrait', 'histTrait'));
        $classGenerator->addTraitOverride('myTrait::foo', 'hisTrait');

        $overrides = $classGenerator->getTraitOverrides();
        $this->assertEquals(count($overrides), 1);
        $this->assertEquals(key($overrides), 'myTrait::foo');
        $this->assertEquals($overrides['myTrait::foo'][0], 'hisTrait');
    }

    public function testCanAddMultipleTraitOverrides()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTraits(array('myTrait', 'histTrait', 'thatTrait'));
        $classGenerator->addTraitOverride('myTrait::foo', array('hisTrait', 'thatTrait'));

        $overrides = $classGenerator->getTraitOverrides();
        $this->assertEquals(count($overrides['myTrait::foo']), 2);
        $this->assertEquals($overrides['myTrait::foo'][1], 'thatTrait');
    }

    public function testAddTraitOverrideExceptionInvalidMethodFormat()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Format: $method must be in the format of trait::method'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitOverride('method', 'useMe');
    }

    public function testAddTraitOverrideExceptionInvalidMethodTraitDoesNotExist()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid trait: Trait does not exists on this class'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitOverride('unknown::method', 'useMe');
    }

    public function testAddTraitOverrideExceptionInvalidTraitName()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Missing required argument "traitName" for $method'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitOverride(array('method' => 'foo'), 'test');
    }

    public function testAddTraitOverrideExceptionInvalidTraitToReplaceArgument()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Invalid Argument: $traitToReplace must be a string or array of strings'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitOverride('myTrait::method', array('methodOne', 4));
    }

    public function testAddTraitOverrideExceptionInvalidMethodArgInArray()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'Missing required argument "method" for $method'
        );

        $classGenerator->addTrait('myTrait');
        $classGenerator->addTraitOverride(array('traitName' => 'myTrait'), 'test');
    }

    public function testCanRemoveTraitOverride()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTraits(array('myTrait', 'histTrait', 'thatTrait'));
        $classGenerator->addTraitOverride('myTrait::foo', array('hisTrait', 'thatTrait'));

        $overrides = $classGenerator->getTraitOverrides();
        $this->assertEquals(count($overrides['myTrait::foo']), 2);

        $classGenerator->removeTraitOverride('myTrait::foo', 'hisTrait');
        $overrides = $classGenerator->getTraitOverrides();

        $this->assertEquals(count($overrides['myTrait::foo']), 1);
        $this->assertEquals($overrides['myTrait::foo'][1], 'thatTrait');
    }

    public function testCanRemoveAllTraitOverrides()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->addTraits(array('myTrait', 'histTrait', 'thatTrait'));
        $classGenerator->addTraitOverride('myTrait::foo', array('hisTrait', 'thatTrait'));

        $overrides = $classGenerator->getTraitOverrides();
        $this->assertEquals(count($overrides['myTrait::foo']), 2);

        $classGenerator->removeTraitOverride('myTrait::foo');
        $overrides = $classGenerator->getTraitOverrides();

        $this->assertEquals(count($overrides), 0);
    }

    /**
     * @group generate
     */
    public function testUseTraitGeneration()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->setName('myClass');
        $classGenerator->addTrait('myTrait');
        $classGenerator->addTrait('hisTrait');
        $classGenerator->addTrait('thatTrait');

        $output = <<<'CODE'
class myClass
{

    use myTrait, hisTrait, thatTrait;


}

CODE;
        $this->assertEquals($classGenerator->generate(), $output);
    }

    /**
     * @group generate
     */
    public function testTraitGenerationWithAliasesAndOverrides()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->setName('myClass');
        $classGenerator->addTrait('myTrait');
        $classGenerator->addTrait('hisTrait');
        $classGenerator->addTrait('thatTrait');
        $classGenerator->addTraitAlias("hisTrait::foo", "test", ReflectionMethod::IS_PUBLIC);
        $classGenerator->addTraitOverride('myTrait::bar', array('hisTrait', 'thatTrait'));

        $output = <<<'CODE'
class myClass
{

    use myTrait, hisTrait, thatTrait {
        hisTrait::foo as public test;
        myTrait::bar insteadof hisTrait;
        myTrait::bar insteadof thatTrait;

    }


}

CODE;
        $this->assertEquals($classGenerator->generate(), $output);
    }

    public function testGenerateWithFinalFlag()
    {
        $classGenerator = ClassGenerator::fromArray(array(
            'name' => 'SomeClass',
            'flags' => ClassGenerator::FLAG_FINAL
        ));

        $expectedOutput = <<<EOS
final class SomeClass
{


}

EOS;

        $output = $classGenerator->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }
}
