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
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;

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
class PropertyGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testPropertyConstructor()
    {
        $codeGenProperty = new PropertyGenerator();
        $this->isInstanceOf($codeGenProperty, 'Zend\Code\Generator\PropertyGenerator');
    }

    /**
     * @return array
     */
    public function dataSetTypeSetValueGenerate()
    {
        return array(
            array('string', 'foo', "'foo';"),
            array('int', 1, "1;"),
            array('integer', 1, "1;"),
            array('bool', true, "true;"),
            array('bool', false, "false;"),
            array('boolean', true, "true;"),
            array('number', 1, '1;'),
            array('float', 1.23, '1.23;'),
            array('double', 1.23, '1.23;'),
            array('constant', 'FOO', 'FOO;'),
            array('null', null, 'null;'),
        );
    }

    /**
     * @dataProvider dataSetTypeSetValueGenerate
     * @param string $type
     * @param mixed $value
     * @param string $code
     */
    public function testSetTypeSetValueGenerate($type, $value, $code)
    {
        $defaultValue = new PropertyValueGenerator();
        $defaultValue->setType($type);
        $defaultValue->setValue($value);

        $this->assertEquals($type, $defaultValue->getType());
        $this->assertEquals($code, $defaultValue->generate());
    }

    /**
     * @dataProvider dataSetTypeSetValueGenerate
     * @param string $type
     * @param mixed $value
     * @param string $code
     */
    public function testSetBogusTypeSetValueGenerateUseAutoDetection($type, $value, $code)
    {
        if($type == 'constant') {
            return; // constant can only be detected explicitly
        }

        $defaultValue = new PropertyValueGenerator();
        $defaultValue->setType("bogus");
        $defaultValue->setValue($value);

        $this->assertEquals($code, $defaultValue->generate());
    }

    public function testPropertyReturnsSimpleValue()
    {
        $codeGenProperty = new PropertyGenerator('someVal', 'some string value');
        $this->assertEquals('    public $someVal = \'some string value\';', $codeGenProperty->generate());
    }

    public function testPropertyMultilineValue()
    {
        $targetValue = array(
            5,
            'one' => 1,
            'two' => '2',
            'null' => null,
            'true' => true,
            "bar's" => "bar's",
            );

        $expectedSource = <<<EOS
    public \$myFoo = array(
        5,
        'one' => 1,
        'two' => '2',
        'null' => null,
        'true' => true,
        'bar\'s' => 'bar\'s'
        );
EOS;

        $property = new PropertyGenerator('myFoo', $targetValue);

        $targetSource = $property->generate();
        $targetSource = str_replace("\r", '', $targetSource);
            
        $this->assertEquals($expectedSource, $targetSource);
    }

    public function testPropertyCanProduceContstantModifier()
    {
        $codeGenProperty = new PropertyGenerator('someVal', 'some string value', PropertyGenerator::FLAG_CONSTANT);
        $this->assertEquals('    const someVal = \'some string value\';', $codeGenProperty->generate());
    }

    /**
     * @group PR-704
     */
    public function testPropertyCanProduceContstantModifierWithSetter()
    {
        $codeGenProperty = new PropertyGenerator('someVal', 'some string value');
        $codeGenProperty->setConst(true);
        $this->assertEquals('    const someVal = \'some string value\';', $codeGenProperty->generate());
    }

    public function testPropertyCanProduceStaticModifier()
    {
        $codeGenProperty = new PropertyGenerator('someVal', 'some string value', PropertyGenerator::FLAG_STATIC);
        $this->assertEquals('    public static $someVal = \'some string value\';', $codeGenProperty->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testPropertyWillLoadFromReflection()
    {
        $reflectionClass = new \Zend\Code\Reflection\ClassReflection('\ZendTest\Code\Generator\TestAsset\TestClassWithManyProperties');

        // test property 1
        $reflProp = $reflectionClass->getProperty('_bazProperty');

        $cgProp = PropertyGenerator::fromReflection($reflProp);

        $this->assertEquals('_bazProperty', $cgProp->getName());
        $this->assertEquals(array(true, false, true), $cgProp->getDefaultValue()->getValue());
        $this->assertEquals('private', $cgProp->getVisibility());

        $reflProp = $reflectionClass->getProperty('_bazStaticProperty');


        // test property 2
        $cgProp = PropertyGenerator::fromReflection($reflProp);

        $this->assertEquals('_bazStaticProperty', $cgProp->getName());
        $this->assertEquals(\ZendTest\Code\Generator\TestAsset\TestClassWithManyProperties::FOO, $cgProp->getDefaultValue()->getValue());
        $this->assertTrue($cgProp->isStatic());
        $this->assertEquals('private', $cgProp->getVisibility());
    }

    /**
     * @group ZF-6444
     */
    public function testPropertyWillEmitStaticModifier()
    {
        $codeGenProperty = new PropertyGenerator(
            'someVal',
            'some string value',
            PropertyGenerator::FLAG_STATIC | PropertyGenerator::FLAG_PROTECTED
        );
        $this->assertEquals('    protected static $someVal = \'some string value\';', $codeGenProperty->generate());
    }

    /**
     * @group ZF-7205
     */
    public function testPropertyCanHaveDocBlock()
    {
        $codeGenProperty = new PropertyGenerator(
            'someVal',
            'some string value',
            PropertyGenerator::FLAG_STATIC | PropertyGenerator::FLAG_PROTECTED
        );

        $codeGenProperty->setDocBlock('@var string $someVal This is some val');

        $expected = <<<EOS
    /**
     * @var string \$someVal This is some val
     */
    protected static \$someVal = 'some string value';
EOS;
        $this->assertEquals($expected, $codeGenProperty->generate());
    }

    public function testOtherTypesThrowExceptionOnGenerate()
    {
        $codeGenProperty = new PropertyGenerator('someVal', new \stdClass());

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\RuntimeException',
            'Type \'stdClass\' is unknown or cannot be used as property default value'
            );

        $codeGenProperty->generate();
    }

}
