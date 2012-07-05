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
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\ValueGenerator;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ParameterGeneratorTest extends \PHPUnit_Framework_TestCase
{


    public function testTypeGetterAndSetterPersistValue()
    {
        $parameterGenerator = new ParameterGenerator();
        $parameterGenerator->setType('Foo');
        $this->assertEquals('Foo', $parameterGenerator->getType());
    }

    public function testNameGetterAndSetterPersistValue()
    {
        $parameterGenerator = new ParameterGenerator();
        $parameterGenerator->setName('Foo');
        $this->assertEquals('Foo', $parameterGenerator->getName());
    }

    public function testDefaultValueGetterAndSetterPersistValue()
    {
        $parameterGenerator = new ParameterGenerator();

        $value = new ValueGenerator('Foo', ValueGenerator::TYPE_CONSTANT);
        $parameterGenerator->setDefaultValue($value);
        $this->assertEquals('Foo', (string) $parameterGenerator->getDefaultValue());
    }

    public function testPositionGetterAndSetterPersistValue()
    {
        $parameterGenerator = new ParameterGenerator();
        $parameterGenerator->setPosition(2);
        $this->assertEquals(2, $parameterGenerator->getPosition());
    }

    public function testGenerateIsCorrect()
    {
        $parameterGenerator = new ParameterGenerator();
        $parameterGenerator->setType('Foo');
        $parameterGenerator->setName('bar');
        $parameterGenerator->setDefaultValue(15);
        $this->assertEquals('Foo $bar = 15', $parameterGenerator->generate());

        $parameterGenerator->setDefaultValue('foo');
        $this->assertEquals('Foo $bar = \'foo\'', $parameterGenerator->generate());
    }

    public function testFromReflectionGetParameterName()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('name');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertEquals('param', $codeGenParam->getName());
    }

    public function testFromReflectionGetParameterType()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('type');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertEquals('stdClass', $codeGenParam->getType());
    }

    public function testFromReflectionGetReference()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('reference');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertTrue($codeGenParam->getPassedByReference());
    }

    public function testFromReflectionGetDefaultValue()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('defaultValue');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $defaultValue = $codeGenParam->getDefaultValue();
        $this->assertEquals('\'foo\'', (string) $defaultValue);
    }

    public function testFromReflectionGetArrayHint()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('fromArray');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertEquals('array', $codeGenParam->getType());
    }

    public function testFromReflectionGetWithNativeType()
    {
        $reflectionParameter = $this->getFirstReflectionParameter('hasNativeDocTypes');
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertNotEquals('int', $codeGenParam->getType());
        $this->assertEquals('', $codeGenParam->getType());
    }

    /**
     * @dataProvider dataFromReflectionGenerate
     * @param string $methodName
     * @param string $expectedCode
     */
    public function testFromReflectionGenerate($methodName, $expectedCode)
    {
        //$this->markTestSkipped('Test may not be necessary any longer');
        $reflectionParameter = $this->getFirstReflectionParameter($methodName);
        $codeGenParam = ParameterGenerator::fromReflection($reflectionParameter);

        $this->assertEquals($expectedCode, $codeGenParam->generate());
    }

    public function dataFromReflectionGenerate()
    {
        return array(
            array('name', '$param'),
            array('type', 'stdClass $bar'),
            array('reference', '&$baz'),
            array('defaultValue', '$value = \'foo\''),
            array('defaultNull', '$value = null'),
            array('fromArray', 'array $array'),
            array('hasNativeDocTypes', '$integer'),
            array('defaultArray', '$array = array()'),
            array('defaultArrayWithValues', '$array = array(1, 2, 3)'),
            array('defaultFalse', '$val = false'),
            array('defaultTrue', '$val = true'),
            array('defaultZero', '$number = 0'),
            array('defaultNumber', '$number = 1234'),
            array('defaultFloat', '$float = 1.34'),
            array('defaultConstant', '$con = \'foo\'')
            );
    }


    /**
     * @param  string $method
     * @return \Zend\Reflection\ReflectionParameter
     */
    protected function getFirstReflectionParameter($method)
    {
        $reflectionClass = new \Zend\Code\Reflection\ClassReflection('ZendTest\Code\Generator\TestAsset\ParameterClass');
        $method = $reflectionClass->getMethod($method);

        $params = $method->getParameters();
        return array_shift($params);
    }
}
