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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** requires */
require_once 'Zend/Reflection/Parameter.php';
require_once 'Zend/CodeGenerator/Php/Parameter.php';

require_once '_files/TestSampleSingleClass.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_ParameterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_CodeGenerator_Php_Parameter
     */
    protected $_parameter = null;

    public function setup()
    {
        $this->_parameter = new Zend_CodeGenerator_Php_Parameter();
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
        $this->_parameter->setDefaultValue('Foo');
        $this->assertEquals('Foo', $this->_parameter->getDefaultValue());
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

    public function testFromReflection_GetParameterName()
    {
        $reflParam = $this->getFirstReflectionParameter('name');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertEquals('param', $codeGenParam->getName());
    }

    public function testFromReflection_GetParameterType()
    {
        $reflParam = $this->getFirstReflectionParameter('type');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertEquals('stdClass', $codeGenParam->getType());
    }

    public function testFromReflection_GetReference()
    {
        $reflParam = $this->getFirstReflectionParameter('reference');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertTrue($codeGenParam->getPassedByReference());
    }

    public function testFromReflection_GetDefaultValue()
    {
        $reflParam = $this->getFirstReflectionParameter('defaultValue');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertEquals('foo', $codeGenParam->getDefaultValue());
    }

    public function testFromReflection_GetArrayHint()
    {
        $reflParam = $this->getFirstReflectionParameter('fromArray');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertEquals('array', $codeGenParam->getType());
    }

    public function testFromReflection_GetWithNativeType()
    {
        $reflParam = $this->getFirstReflectionParameter('hasNativeDocTypes');
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertNotEquals('int', $codeGenParam->getType());
        $this->assertEquals('', $codeGenParam->getType());
    }

    static public function dataFromReflection_Generate()
    {
        return array(
            array('name', '$param'),
            array('type', 'stdClass $bar'),
            array('reference', '&$baz'),
            array('defaultValue', '$value = \'foo\''),
            array('defaultNull', '$value = null'),
            array('fromArray', 'array $array'),
            array('hasNativeDocTypes', '$integer'),
            array('defaultArray', '$array = array ()'),
            array('defaultArrayWithValues', '$array = array (  0 => 1,  1 => 2,  2 => 3,)'),
            array('defaultFalse', '$val = false'),
            array('defaultTrue', '$val = true'),
            array('defaultZero', '$number = 0'),
            array('defaultNumber', '$number = 1234'),
            array('defaultFloat', '$float = 1.34'),
            array('defaultConstant', '$con = \'foo\'')
        );
    }

    /**
     * @dataProvider dataFromReflection_Generate
     * @param string $methodName
     * @param string $expectedCode
     */
    public function testFromReflection_Generate($methodName, $expectedCode)
    {
        $reflParam = $this->getFirstReflectionParameter($methodName);
        $codeGenParam = Zend_CodeGenerator_Php_Parameter::fromReflection($reflParam);

        $this->assertEquals($expectedCode, $codeGenParam->generate());
    }

    /**
     * @param  string $method
     * @return Zend_Reflection_Parameter
     */
    private function getFirstReflectionParameter($method)
    {
        $reflClass = new Zend_Reflection_Class('Zend_CodeGenerator_Php_ParameterExample');
        $method = $reflClass->getMethod($method);

        $params = $method->getParameters();
        return array_shift($params);
    }
}

class Zend_CodeGenerator_Php_ParameterExample
{
    public function name($param)
    {

    }

    public function type(stdClass $bar)
    {

    }

    public function reference(&$baz)
    {

    }

    public function defaultValue($value="foo")
    {
    }

    public function defaultNull($value=null)
    {

    }

    public function fromArray(array $array)
    {

    }

    public function defaultArray($array = array())
    {

    }

    public function defaultFalse($val = false)
    {

    }

    public function defaultTrue($val = true)
    {

    }

    public function defaultZero($number = 0)
    {

    }

    public function defaultNumber($number = 1234)
    {

    }

    public function defaultFloat($float = 1.34)
    {

    }

    public function defaultArrayWithValues($array = array(0 => 1, 1 => 2, 2 => 3))
    {

    }

    const FOO = "foo";

    public function defaultConstant($con = self::FOO)
    {

    }

    /**
     * @param int $integer
     */
    public function hasNativeDocTypes($integer)
    {

    }
}
