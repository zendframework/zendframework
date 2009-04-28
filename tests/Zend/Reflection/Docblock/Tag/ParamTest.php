<?php

require_once 'Zend/Reflection/File.php';

/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Docblock
 * @group Zend_Reflection_Docblock_Tag
 * @group Zend_Reflection_Docblock_Tag_Param
 */
class Zend_Reflection_Docblock_Tag_ParamTest extends PHPUnit_Framework_TestCase
{
    

    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(dirname(__FILE__))) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
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
    
}
    