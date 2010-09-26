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
class PhpPropertyTest extends \PHPUnit_Framework_TestCase
{

    public function testPropertyConstructor()
    {
        $codeGenProperty = new Php\PhpProperty();
        $this->isInstanceOf($codeGenProperty, '\Zend\CodeGenerator\Php\PhpProperty');
    }

    public function testPropertyReturnsSimpleValue()
    {
        $codeGenProperty = new Php\PhpProperty(array('name' => 'someVal', 'defaultValue' => 'some string value'));
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

        $property = new Php\PhpProperty(array(
            'name' => 'myFoo',
            'defaultValue' => $targetValue
            ));

        $targetSource = $property->generate();
        $targetSource = str_replace("\r", '', $targetSource);
            
        $this->assertEquals($expectedSource, $targetSource);
    }

    public function testPropertyCanProduceContstantModifier()
    {
        $codeGenProperty = new Php\PhpProperty(array('name' => 'someVal', 'defaultValue' => 'some string value', 'const' => true));
        $this->assertEquals('    const someVal = \'some string value\';', $codeGenProperty->generate());
    }

    public function testPropertyCanProduceStaticModifier()
    {
        $codeGenProperty = new Php\PhpProperty(array('name' => 'someVal', 'defaultValue' => 'some string value', 'static' => true));
        $this->assertEquals('    public static $someVal = \'some string value\';', $codeGenProperty->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testPropertyWillLoadFromReflection()
    {
        $reflectionClass = new \Zend\Reflection\ReflectionClass('\ZendTest\CodeGenerator\Php\TestAsset\TestClassWithManyProperties');

        // test property 1
        $reflProp = $reflectionClass->getProperty('_bazProperty');

        $cgProp = Php\PhpProperty::fromReflection($reflProp);

        $this->assertEquals('_bazProperty', $cgProp->getName());
        $this->assertEquals(array(true, false, true), $cgProp->getDefaultValue()->getValue());
        $this->assertEquals('private', $cgProp->getVisibility());

        $reflProp = $reflectionClass->getProperty('_bazStaticProperty');


        // test property 2
        $cgProp = Php\PhpProperty::fromReflection($reflProp);

        $this->assertEquals('_bazStaticProperty', $cgProp->getName());
        $this->assertEquals(\ZendTest\CodeGenerator\Php\TestAsset\TestClassWithManyProperties::FOO, $cgProp->getDefaultValue()->getValue());
        $this->assertTrue($cgProp->isStatic());
        $this->assertEquals('private', $cgProp->getVisibility());
    }

    /**
     * @group ZF-6444
     */
    public function testPropertyWillEmitStaticModifier()
    {
        $codeGenProperty = new Php\PhpProperty(array(
            'name' => 'someVal',
            'static' => true,
            'visibility' => 'protected',
            'defaultValue' => 'some string value'
            ));
        $this->assertEquals('    protected static $someVal = \'some string value\';', $codeGenProperty->generate());
    }

    /**
     * @group ZF-7205
     */
    public function testPropertyCanHaveDocblock()
    {
        $codeGenProperty = new Php\PhpProperty(array(
            'name' => 'someVal',
            'static' => true,
            'visibility' => 'protected',
            'defaultValue' => 'some string value',
            'docblock' => '@var string $someVal This is some val'
            ));

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
        $codeGenProperty = new Php\PhpProperty(array(
            'name' => 'someVal',
            'defaultValue' => new \stdClass(),
        ));

        $this->setExpectedException('Zend\CodeGenerator\Php\Exception');

        $codeGenProperty->generate();
    }

    static public function dataSetTypeSetValueGenerate()
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
        $defaultValue = new Php\PhpPropertyValue();
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

        $defaultValue = new Php\PhpPropertyValue();
        $defaultValue->setType("bogus");
        $defaultValue->setValue($value);

        $this->assertEquals($code, $defaultValue->generate());
    }
    
    /**
     * @group ZF-8849
     */
    public function testZF8849()
    {
        $property = new Php\PhpProperty(array(
            'defaultValue' => array('value' => 1.337, 'type' => 'string'),
            'name'         => 'ZF8849',
            'const'        => true
        ));
        
        $this->assertEquals(
            "    const ZF8849 = '1.337';",
            $property->generate()
        );
    }
}
