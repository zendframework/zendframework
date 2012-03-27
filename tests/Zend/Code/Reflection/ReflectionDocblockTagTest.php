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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Code\Reflection\Docblock;
use Zend\Code\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 * @group      Zend_Reflection_Docblock_Tag
 */
class ReflectionDocblockTagTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->markTestIncomplete('Not refactored yet');
    }

    public function testTagDescriptionIsReturned()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $authorTag = $classReflection->getDocblock()->getTag('author');
        $this->assertEquals('Ralph Schindler <ralph.schindler@zend.com>', $authorTag->getDescription());
    }

    public function testTagShouldAllowJustTagNameInDocblockTagLine()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('emptyTag');
        $this->assertEquals($tag->getName(), 'emptyTag', 'Factory First Match Failed');
    }

    public function testTagShouldAllowMultipleWhitespacesBeforeDescription()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('descriptionTag');
        $this->assertEquals('          A tag with just a description', $tag->getDescription(), 'Final Match Failed');
        $this->assertEquals('A tag with just a description', $tag->getDescription('trimWhitespace'), 'Final Match Failed');
    }

    public function testToString()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('descriptionTag');

        $expectedString = "DocBlock Tag [ * @descriptionTag ]".PHP_EOL;

        $this->assertEquals($expectedString, (string)$tag);
    }


    public function testTypeParam()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');

        $this->assertEquals($paramTag->getType(), 'int');
    }

    public function testVariableName()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');
        $this->assertEquals($paramTag->getVariable(), '$one');
    }

    public function testAllowsMultipleSpacesInDocblockTagLine()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');

        $trimOpt = Reflection\ReflectionDocblockTag::TRIM_WHITESPACE;
        
        $this->assertEquals($paramTag->getType($trimOpt), 'int', 'Second Match Failed');
        $this->assertEquals($paramTag->getVariable($trimOpt), '$var', 'Third Match Failed');
        $this->assertEquals($paramTag->getDescription($trimOpt),'Description of $var', 'Final Match Failed');
    }


    /**
     * @group ZF-8307
     */
    public function testNamespaceInParam()
    {    
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass7');
        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');

        $trimOpt = Reflection\ReflectionDocblockTag::TRIM_WHITESPACE;
        
        $this->assertEquals('Zend\Foo\Bar', $paramTag->getType($trimOpt));
        $this->assertEquals('$var', $paramTag->getVariable($trimOpt));
        $this->assertEquals('desc', $paramTag->getDescription($trimOpt));
    }
    
    public function testType()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals($paramTag->getType(), 'mixed');
    }

    public function testAllowsMultipleSpacesInDocblockTagLine2()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass6');

        $trimOpt = Reflection\ReflectionDocblockTag::TRIM_WHITESPACE;
        
        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');

        $this->assertEquals($paramTag->getType($trimOpt), 'string', 'Second Match Failed');
        $this->assertEquals($paramTag->getDescription($trimOpt),'Description of return value', 'Final Match Failed');
    }
    


    /**
     * @group ZF-8307
     */
    public function testReturnClassWithNamespace()
    {
        $classReflection = new Reflection\ReflectionClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass7');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');

        $trimOpt = Reflection\ReflectionDocblockTag::TRIM_WHITESPACE;
        $this->assertEquals('Zend\Code\Reflection\DocBlock', $paramTag->getType($trimOpt));
    }
    
    
    
}









