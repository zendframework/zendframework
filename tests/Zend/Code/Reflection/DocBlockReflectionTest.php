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

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\DocBlockReflection;
use Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class DocBlockReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDocBlockShortDescription()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');
        $this->assertEquals('TestSampleClass5 DocBlock Short Desc', $classReflection->getDocBlock()->getShortDescription());
    }

    public function testDocBlockLongDescription()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');
        $expectedOutput = 'This is a long description for the docblock of this class, it should be longer than 3 lines. It indeed is longer than 3 lines now.';


        $this->assertEquals($expectedOutput, $classReflection->getDocBlock()->getLongDescription());

    }

    public function testDocBlockTags()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(3, count($classReflection->getDocBlock()->getTags()));
        $this->assertEquals(1, count($classReflection->getDocBlock()->getTags('author')));
        $this->assertEquals(1, count($classReflection->getDocBlock()->getTags('property')));
        $this->assertEquals(1, count($classReflection->getDocBlock()->getTags('method')));

        $methodTag = $classReflection->getDocBlock()->getTag('method');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\MethodTag', $methodTag);

        $propertyTag = $classReflection->getDocBlock()->getTag('property');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\PropertyTag', $propertyTag);

        $this->assertFalse($classReflection->getDocBlock()->getTag('version'));

        $this->assertTrue($classReflection->getMethod('doSomething')->getDocBlock()->hasTag('return'));

        $returnTag = $classReflection->getMethod('doSomething')->getDocBlock()->getTag('return');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\TagInterface', $returnTag);
        $this->assertEquals('mixed', $returnTag->getType());


    }

    public function testDocBlockLines()
    {
        //$this->markTestIncomplete('Line numbers incomplete');

        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocBlock = $classReflection->getDocBlock();

        $this->assertEquals(5, $classDocBlock->getStartLine());
        $this->assertEquals(17, $classDocBlock->getEndLine());

    }

    public function testDocBlockContents()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocBlock = $classReflection->getDocBlock();

        $expectedContents = <<<EOS
TestSampleClass5 DocBlock Short Desc

This is a long description for
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.

@author Ralph Schindler <ralph.schindler@zend.com>
@method test()
@property \$test

EOS;

        $this->assertEquals($expectedContents, $classDocBlock->getContents());

    }

    public function testToString()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocBlock = $classReflection->getDocBlock();

        $expectedString = 'DocBlock [ /* DocBlock */ ] {' . PHP_EOL
                        . PHP_EOL
                        . '  - Tags [3] {' . PHP_EOL
                        . '    DocBlock Tag [ * @author ]' . PHP_EOL
                        . '    DocBlock Tag [ * @method ]' . PHP_EOL
                        . '    DocBlock Tag [ * @property ]' . PHP_EOL
                        . '  }' . PHP_EOL
                        . '}' . PHP_EOL;

        $this->assertEquals($expectedString, (string)$classDocBlock);
    }
}
