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

/** requires */
require_once 'Zend/Reflection/Method.php';
require_once 'Zend/CodeGenerator/Php/Method.php';
require_once 'Zend/Reflection/Docblock.php';
require_once 'Zend/Reflection/Class.php';

require_once '_files/TestSampleSingleClass.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_MethodTest extends PHPUnit_Framework_TestCase
{


    /**
     * @var Zend_CodeGenerator_Php_Method
     */
    protected $_method = null;
    
    public function setup()
    {
        $this->_method = new Zend_CodeGenerator_Php_Method();
    }

    public function teardown()
    {
        $this->_method = null;
    }
    
    public function testConstructor()
    {
        $codeGenMethod = new Zend_CodeGenerator_Php_Method();
        $this->isInstanceOf($codeGenMethod, 'Zend_CodeGenerator_Php_Method');
    }
    
    public function testParameterAccessors()
    {
        $codeGen = new Zend_CodeGenerator_Php_Method();
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $params = $codeGen->getParameters();
        $param = array_shift($params);
        $this->assertTrue($param instanceof Zend_CodeGenerator_Php_Parameter, 'Failed because $param was not instance of Zend_CodeGenerator_Php_Property');
    }
    
    public function testBodyGetterAndSetter()
    {
        $this->_method->setBody('Foo');
        $this->assertEquals('Foo', $this->_method->getBody());
    }
    
    public function testDocblockGetterAndSetter()
    {
        $d = new Zend_CodeGenerator_Php_Docblock();
        
        $this->_method->setDocblock($d);
        $this->assertTrue($d === $this->_method->getDocblock());
    }
    
    
    public function testFromReflection()
    {
        $ref = new Zend_Reflection_Method('Zend_Reflection_TestSampleSingleClass', 'someMethod');
        
        $codeGenMethod = Zend_CodeGenerator_Php_Method::fromReflection($ref);
        $target = <<<EOS
    /**
     * Enter description here...
     * 
     * @return bool
     * 
     */
    public function someMethod()
    {
        /* test test */
    }

EOS;
        $this->assertEquals($target, (string) $codeGenMethod);
    }
    
    /**
     * @group ZF-6444
     */
    public function testStaticModifierIsEmitted()
    {
        $codeGen = new Zend_CodeGenerator_Php_Method();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setStatic(true);
        
        $expected = <<<EOS
    public static function foo(\$one)
    {
    }

EOS;
            
        $this->assertEquals($expected, $codeGen->generate());
    }
    
    /**
     * @group ZF-6444
     */
    public function testFinalModifierIsEmitted()
    {
        $codeGen = new Zend_CodeGenerator_Php_Method();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setFinal(true);
        
        $expected = <<<EOS
    final public function foo(\$one)
    {
    }

EOS;
        $this->assertEquals($expected, $codeGen->generate());
    }
    
    /**
     * @group ZF-6444
     */
    public function testFinalModifierIsNotEmittedWhenMethodIsAbstract()
    {
        $codeGen = new Zend_CodeGenerator_Php_Method();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setFinal(true);
        $codeGen->setAbstract(true);
        
        $expected = <<<EOS
    abstract public function foo(\$one)
    {
    }

EOS;
        $this->assertEquals($expected, $codeGen->generate());
    }
    
}
