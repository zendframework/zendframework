<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 * @group      Zend_Reflection_DocBlock_Tag
 */
class ReflectionDocBlockTagTest extends \PHPUnit_Framework_TestCase
{
    public function testTagDescriptionIsReturned()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $authorTag = $classReflection->getDocBlock()->getTag('author');
        $this->assertEquals('Ralph Schindler', $authorTag->getAuthorName());
        $this->assertEquals('ralph.schindler@zend.com', $authorTag->getAuthorEmail());
    }

    public function testTagShouldAllowJustTagNameInDocBlockTagLine()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('emptyTag');
        $this->assertEquals($tag->getName(), 'emptyTag', 'Factory First Match Failed');
    }

    public function testTagShouldAllowMultipleWhitespacesBeforeDescription()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('descriptionTag');
        $this->assertNotEquals('          A tag with just a description', $tag->getContent(), 'Final Match Failed');
        $this->assertEquals('A tag with just a description', $tag->getContent(), 'Final Match Failed');
    }

    public function testToString()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('descriptionTag');

        $expectedString = 'DocBlock Tag [ * @descriptionTag ]' . PHP_EOL;

        $this->assertEquals($expectedString, (string)$tag);
    }


    public function testTypeParam()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('param');

        $this->assertEquals($paramTag->getType(), 'int');
    }

    public function testVariableName()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('param');
        $this->assertEquals($paramTag->getVariableName(), '$one');
    }

    public function testAllowsMultipleSpacesInDocBlockTagLine()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('param');


        $this->assertEquals($paramTag->getType(), 'int', 'Second Match Failed');
        $this->assertEquals($paramTag->getVariableName(), '$var', 'Third Match Failed');
        $this->assertEquals($paramTag->getDescription(),'Description of $var', 'Final Match Failed');
    }


    /**
     * @group ZF-8307
     */
    public function testNamespaceInParam()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass7');
        $paramTag        = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('param');


        $this->assertEquals('Zend\Foo\Bar', $paramTag->getType());
        $this->assertEquals('$var', $paramTag->getVariableName());
        $this->assertEquals('desc', $paramTag->getDescription());
    }

    public function testType()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('return');
        $this->assertEquals($paramTag->getType(), 'mixed');
    }

    public function testAllowsMultipleSpacesInDocBlockTagLine2()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('return');

        $this->assertEquals($paramTag->getType(), 'string', 'Second Match Failed');
        $this->assertEquals($paramTag->getDescription(), 'Description of return value', 'Final Match Failed');
    }


    /**
     * @group ZF-8307
     */
    public function testReturnClassWithNamespace()
    {
        $classReflection = new Reflection\ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass7');

        $paramTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('return');

        $this->assertEquals('Zend\Code\Reflection\DocBlock', $paramTag->getType());
    }
}
