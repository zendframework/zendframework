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
class PhpParameterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\CodeGenerator\Php\PhpParameter
     */
    protected $_parameter = null;

    public function setup()
    {
        $this->_parameter = new Php\PhpParameter();
    }

    public function teardown()
    {
        $this->_parameter = null;
    }

    public function testTypeGetterAndSetterPersistValue()
    {
        $this->_parameter->setType('Foo');
        $this->assertEquals('Foo', $this->_parameter->getType());
    }

    public function testNameGetterAndSetterPersistValue()
    {
        $this->_parameter->setName('Foo');
        $this->assertEquals('Foo', $this->_parameter->getName());
    }

    public function testDefaultValueGetterAndSetterPersistValue()
    {
        $value = new Php\PhpParameterDefaultValue(array('value'=>'Foo','type'=>'constant'));
        $this->_parameter->setDefaultValue($value);
        $this->assertEquals('Foo;', (string) $this->_parameter->getDefaultValue());
    }

    public function testPositionGetterAndSetterPersistValue()
    {
        $this->_parameter->setPosition(2);
        $this->assertEquals(2, $this->_parameter->getPosition());
    }

    public function testGenerateIsCorrect()
    {
        $this->_parameter->setType('Foo');
        $this->_parameter->setName('bar');
        $this->_parameter->setDefaultValue(15);
        $this->assertEquals('Foo $bar = 15', $this->_parameter->generate());

        $this->_parameter->setDefaultValue('foo');
        $this->assertEquals('Foo $bar = \'foo\'', $this->_parameter->generate());
    }

    public function testFromReflectionGetParameterName()
    {
        $reflParam = $this->_getFirstReflectionParameter('name');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertEquals('param', $codeGenParam->getName());
    }

    public function testFromReflectionGetParameterType()
    {
        $reflParam = $this->_getFirstReflectionParameter('type');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertEquals('stdClass', $codeGenParam->getType());
    }

    public function testFromReflectionGetReference()
    {
        $reflParam = $this->_getFirstReflectionParameter('reference');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertTrue($codeGenParam->getPassedByReference());
    }

    public function testFromReflectionGetDefaultValue()
    {
        $reflParam = $this->_getFirstReflectionParameter('defaultValue');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $defaultValue = $codeGenParam->getDefaultValue();
        $this->assertEquals('\'foo\';', (string) $defaultValue);
    }

    public function testFromReflectionGetArrayHint()
    {
        $reflParam = $this->_getFirstReflectionParameter('fromArray');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertEquals('array', $codeGenParam->getType());
    }

    public function testFromReflectionGetWithNativeType()
    {
        $reflParam = $this->_getFirstReflectionParameter('hasNativeDocTypes');
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertNotEquals('int', $codeGenParam->getType());
        $this->assertEquals('', $codeGenParam->getType());
    }

    static public function dataFromReflectionGenerate()
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
     * @dataProvider dataFromReflectionGenerate
     * @param string $methodName
     * @param string $expectedCode
     */
    public function testFromReflectionGenerate($methodName, $expectedCode)
    {
        $this->markTestSkipped('Test may not be necessary any longer');
        $reflParam = $this->_getFirstReflectionParameter($methodName);
        $codeGenParam = Php\PhpParameter::fromReflection($reflParam);

        $this->assertEquals($expectedCode, $codeGenParam->generate());
    }

    /**
     * @param  string $method
     * @return \Zend\Reflection\ReflectionParameter
     */
    private function _getFirstReflectionParameter($method)
    {
        $reflClass = new \Zend\Reflection\ReflectionClass('\ZendTest\CodeGenerator\Php\TestAsset\ParameterClass');
        $method = $reflClass->getMethod($method);

        $params = $method->getParameters();
        return array_shift($params);
    }
}
