<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Reflection\MethodReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class MethodGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodConstructor()
    {
        $methodGenerator = new MethodGenerator();
        $this->isInstanceOf($methodGenerator, '\Zend\Code\Generator\PhpMethod');
    }

    public function testMethodParameterAccessors()
    {
        $methodGenerator = new MethodGenerator();
        $methodGenerator->setParameters(array('one'));
        $params = $methodGenerator->getParameters();
        $param = array_shift($params);
        $this->assertInstanceOf('Zend\Code\Generator\ParameterGenerator', $param);
    }

    public function testMethodParameterMutator()
    {
        $methodGenerator = new MethodGenerator();

        $methodGenerator->setParameter('foo');
        $methodGenerator->setParameter(array('name' => 'bar', 'type' => 'array'));
        $methodGenerator->setParameter(ParameterGenerator::fromArray(array('name' => 'baz', 'type' => '\stdClass')));

        $params = $methodGenerator->getParameters();
        $this->assertCount(3, $params);

        /** @var $foo ParameterGenerator */
        $foo = array_shift($params);
        $this->assertInstanceOf('Zend\Code\Generator\ParameterGenerator', $foo);
        $this->assertEquals('foo', $foo->getName());

        $bar = array_shift($params);
        $this->assertEquals(ParameterGenerator::fromArray(array('name' => 'bar', 'type' => 'array')), $bar);

        /** @var $baz ParameterGenerator */
        $baz = array_shift($params);
        $this->assertEquals('baz', $baz->getName());

        $this->setExpectedException('Zend\Code\Generator\Exception\InvalidArgumentException');
        $methodGenerator->setParameter(new \stdClass());
    }

    public function testMethodBodyGetterAndSetter()
    {
        $method = new MethodGenerator();
        $method->setBody('Foo');
        $this->assertEquals('Foo', $method->getBody());
    }

    public function testDocBlockGetterAndSetter()
    {
        $docblockGenerator = new \Zend\Code\Generator\DocBlockGenerator();

        $method = new MethodGenerator();
        $method->setDocBlock($docblockGenerator);
        $this->assertSame($docblockGenerator, $method->getDocBlock());
    }


    public function testMethodFromReflection()
    {
        $ref = new MethodReflection('ZendTest\Code\Generator\TestAsset\TestSampleSingleClass', 'someMethod');

        $methodGenerator = MethodGenerator::fromReflection($ref);
        $target = <<<EOS
    /**
     * Enter description here...
     *
     * @return bool
     */
    public function someMethod()
    {
        /* test test */
    }

EOS;
        $this->assertEquals($target, (string) $methodGenerator);
    }


    public function testMethodFromReflectionMultiLinesIndention()
    {
        $ref = new MethodReflection('ZendTest\Code\Generator\TestAsset\TestSampleSingleClassMultiLines', 'someMethod');

        $methodGenerator = MethodGenerator::fromReflection($ref);
        $target = <<<EOS
    /**
     * Enter description here...
     *
     * @return bool
     */
    public function someMethod()
    {
        /* test test */

        /* test test */

        /* test test */
    }

EOS;
        $this->assertEquals($target, (string) $methodGenerator);
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithStaticModifierIsEmitted()
    {
        $methodGenerator = new MethodGenerator();
        $methodGenerator->setName('foo');
        $methodGenerator->setParameters(array('one'));
        $methodGenerator->setStatic(true);

        $expected = <<<EOS
    public static function foo(\$one)
    {
    }

EOS;

        $this->assertEquals($expected, $methodGenerator->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithFinalModifierIsEmitted()
    {
        $methodGenerator = new MethodGenerator();
        $methodGenerator->setName('foo');
        $methodGenerator->setParameters(array('one'));
        $methodGenerator->setFinal(true);

        $expected = <<<EOS
    final public function foo(\$one)
    {
    }

EOS;
        $this->assertEquals($expected, $methodGenerator->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithFinalModifierIsNotEmittedWhenMethodIsAbstract()
    {
        $methodGenerator = new MethodGenerator();
        $methodGenerator->setName('foo');
        $methodGenerator->setParameters(array('one'));
        $methodGenerator->setFinal(true);
        $methodGenerator->setAbstract(true);

        $expected = <<<EOS
    abstract public function foo(\$one);
EOS;
        $this->assertEquals($expected, $methodGenerator->generate());
    }

    /**
     * @group ZF-7205
     */
    public function testMethodCanHaveDocBlock()
    {
        $methodGeneratorProperty = new MethodGenerator(
            'someFoo',
            array(),
            MethodGenerator::FLAG_STATIC | MethodGenerator::FLAG_PROTECTED,
            null,
            '@var string $someVal This is some val'
        );

        $expected = <<<EOS
    /**
     * @var string \$someVal This is some val
     */
    protected static function someFoo()
    {
    }

EOS;
        $this->assertEquals($expected, $methodGeneratorProperty->generate());
    }

    /**
     * @group ZF-7268
     */
    public function testDefaultValueGenerationDoesNotIncludeTrailingSemicolon()
    {
        $method = new MethodGenerator('setOptions');
        $default = new ValueGenerator();
        $default->setValue(array());

        $param   = new ParameterGenerator('options', 'array');
        $param->setDefaultValue($default);

        $method->setParameter($param);
        $generated = $method->generate();
        $this->assertRegexp('/array \$options = array\(\)\)/', $generated, $generated);
    }

    public function testCreateFromArray()
    {
        $methodGenerator = MethodGenerator::fromArray(array(
            'name'       => 'SampleMethod',
            'body'       => 'foo',
            'docblock'   => array(
                'shortdescription' => 'foo',
            ),
            'abstract'   => true,
            'final'      => true,
            'static'     => true,
            'visibility' => MethodGenerator::VISIBILITY_PROTECTED,
        ));

        $this->assertEquals('SampleMethod', $methodGenerator->getName());
        $this->assertEquals('foo', $methodGenerator->getBody());
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $methodGenerator->getDocBlock());
        $this->assertTrue($methodGenerator->isAbstract());
        $this->assertTrue($methodGenerator->isFinal());
        $this->assertTrue($methodGenerator->isStatic());
        $this->assertEquals(MethodGenerator::VISIBILITY_PROTECTED, $methodGenerator->getVisibility());
    }
}
