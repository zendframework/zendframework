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
}
