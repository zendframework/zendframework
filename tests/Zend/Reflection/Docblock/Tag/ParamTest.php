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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 * @group      Zend_Reflection_Docblock_Tag
 * @group      Zend_Reflection_Docblock_Tag_Param
 */
class Zend_Reflection_Docblock_Tag_ParamTest extends PHPUnit_Framework_TestCase
{


    static protected $_sampleClassFileRequired = false;

    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(dirname(__FILE__))) . '/_files/TestSampleClass.php';
            self::$_sampleClassFileRequired = true;
        }
    }

    public function testType()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');
        $this->assertEquals($paramTag->getType(), 'int');
    }

    public function testVariableName()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');
        $this->assertEquals($paramTag->getVariableName(), '$one');
    }

    public function testAllowsMultipleSpacesInDocblockTagLine()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass6');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');

        $this->assertEquals($paramTag->getType(), 'int', 'Second Match Failed');
        $this->assertEquals($paramTag->getVariableName(), '$var', 'Third Match Failed');
        $this->assertEquals($paramTag->getDescription(),'Description of $var', 'Final Match Failed');
    }

    /**
     * @group ZF-8307
     */
    public function testNamespaceInParam()
    {    
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_Docblock_Param_WithNamespace');
        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');

        $this->assertEquals('Zend\Foo\Bar', $paramTag->getType());
        $this->assertEquals('$var', $paramTag->getVariableName());
        $this->assertEquals('desc', $paramTag->getDescription());
    }
}

