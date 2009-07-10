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
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/CodeGenerator/Php/Property.php';

require_once 'Zend/Reflection/Class.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_PropertyTest extends PHPUnit_Framework_TestCase
{
    
    public function setup()
    {
        if (!class_exists('Zend_CodeGenerator_Php_TestClassWithManyProperties')) {
            require_once dirname(__FILE__) . '/_files/TestClassWithManyProperties.php';
        }
    }
    
    public function testPropertyConstructor()
    {
        $codeGenProperty = new Zend_CodeGenerator_Php_Property();
        $this->isInstanceOf($codeGenProperty, 'Zend_CodeGenerator_Php_Property');
    }
    
    public function testPropertyReturnsSimpleValue()
    {
        $codeGenProperty = new Zend_CodeGenerator_Php_Property(array('name' => 'someVal', 'defaultValue' => 'some string value'));
        $this->assertEquals('    public $someVal = \'some string value\';', $codeGenProperty->generate());
    }
    
    public function testPropertyMultilineValue()
    {
        $targetValue = array(
            5, 
            'one' => 1,
            'two' => '2',
            );
        
        $expectedSource = <<<EOS
    public \$myFoo = array(
        5,
        'one' => 1,
        'two' => '2'
        );
EOS;

        $property = new Zend_CodeGenerator_Php_Property(array(
            'name' => 'myFoo',
            'defaultValue' => $targetValue
            ));
        
        $this->assertEquals($expectedSource, $property->generate());
    }
    
    public function testPropertyCanProduceContstantModifier()
    {
        $codeGenProperty = new Zend_CodeGenerator_Php_Property(array('name' => 'someVal', 'defaultValue' => 'some string value', 'const' => true));
        $this->assertEquals('    const someVal = \'some string value\';', $codeGenProperty->generate());
    }
    
    public function testPropertyCanProduceStaticModifier()
    {
        $codeGenProperty = new Zend_CodeGenerator_Php_Property(array('name' => 'someVal', 'defaultValue' => 'some string value', 'static' => true));
        $this->assertEquals('    public static $someVal = \'some string value\';', $codeGenProperty->generate());
    }
    
    /**
     * @group ZF-6444
     */
    public function testPropertyWillLoadFromReflection()
    {
        $reflectionClass = new Zend_Reflection_Class('Zend_CodeGenerator_Php_TestClassWithManyProperties');
        
        // test property 1       
        $reflProp = $reflectionClass->getProperty('_bazProperty');
       
        $cgProp = Zend_CodeGenerator_Php_Property::fromReflection($reflProp);
        
        $this->assertEquals('_bazProperty', $cgProp->getName());
        $this->assertEquals(array(true, false, true), $cgProp->getDefaultValue()->getValue());
        $this->assertEquals('private', $cgProp->getVisibility());
        
        $reflProp = $reflectionClass->getProperty('_bazStaticProperty');
        
        
        // test property 2
        $cgProp = Zend_CodeGenerator_Php_Property::fromReflection($reflProp);
        
        $this->assertEquals('_bazStaticProperty', $cgProp->getName());
        $this->assertEquals(Zend_CodeGenerator_Php_TestClassWithManyProperties::FOO, $cgProp->getDefaultValue()->getValue());
        $this->assertTrue($cgProp->isStatic());
        $this->assertEquals('private', $cgProp->getVisibility());
    }

    /**
     * @group ZF-6444
     */
    public function testPropertyWillEmitStaticModifier()
    {
        $codeGenProperty = new Zend_CodeGenerator_Php_Property(array(
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
        $codeGenProperty = new Zend_CodeGenerator_Php_Property(array(
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
    
}
