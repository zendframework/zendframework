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

/**
 * @see Zend_CodeGenerator_Php_Class
 */
require_once 'Zend/CodeGenerator/Php/Class.php';

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
class Zend_CodeGenerator_Php_ClassTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstruction()
    {
        $class = new Zend_CodeGenerator_Php_Class();
        $this->isInstanceOf($class, 'Zend_CodeGenerator_Php_Class');
    }
    
    public function testNameAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setName('TestClass');
        $this->assertEquals($codeGenClass->getName(), 'TestClass');

    }
    
    public function testClassDocblockAccessors()
    {
        $this->markTestSkipped();
    }
    
    public function testAbstractAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $this->assertFalse($codeGenClass->isAbstract());
        $codeGenClass->setAbstract(true);
        $this->assertTrue($codeGenClass->isAbstract());
    }
    
    public function testExtendedClassAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setExtendedClass('ExtendedClass');
        $this->assertEquals($codeGenClass->getExtendedClass(), 'ExtendedClass');
    }
    
    public function testImplementedInterfacesAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setImplementedInterfaces(array('Class1', 'Class2'));
        $this->assertEquals($codeGenClass->getImplementedInterfaces(), array('Class1', 'Class2'));
    }
    
    public function testPropertyAccessors()
    {

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setProperties(array(
            array('name' => 'propOne'),
            new Zend_CodeGenerator_Php_Property(array('name' => 'propTwo'))
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
        
        try {
            // add a property by a same name
            $codeGenClass->setProperty(array('name' => 'prop3'));
            $this->fail('Zend_CodeGenerator_Php_Exception should have been thrown.');
        } catch (Exception $e) {
            $this->isInstanceOf($e, 'Zend_CodeGenerator_Php_Exception');
        }
        
        
    }
    
    public function testMethodAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setMethods(array(
            array('name' => 'methodOne'),
            new Zend_CodeGenerator_Php_Method(array('name' => 'methodTwo'))
            ));

        $methods = $codeGenClass->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), 'Zend_CodeGenerator_Php_Method');
        
        $method = $codeGenClass->getMethod('methodOne');
        $this->isInstanceOf($method, 'Zend_CodeGenerator_Php_Method');
        $this->assertEquals($method->getName(), 'methodOne');
        
        // add a new property
        $codeGenClass->setMethod(array('name' => 'methodThree'));
        $this->assertEquals(count($codeGenClass->getMethods()), 3);
        
        try {
            // add a property by a same name
            $codeGenClass->setMethod(array('name' => 'methodThree'));
            $this->fail('Zend_CodeGenerator_Php_Exception should have been thrown.');
        } catch (Exception $e) {
            $this->isInstanceOf($e, 'Zend_CodeGenerator_Php_Exception');
        }
    }
    
    public function testToString()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class(array(
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

}
