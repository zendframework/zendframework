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
namespace ZendTest\Code\Reflection;
use Zend\Code\Reflection\DocBlockReflection,
    Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 */
class DocBlockReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDocblockShortDescription()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');
        $this->assertEquals('TestSampleClass5 DocBlock Short Desc', $classReflection->getDocblock()->getShortDescription());
    }

    public function testDocblockLongDescription()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');
        $expectedOutput = 'This is a long description for the docblock of this class, it should be longer than 3 lines. It indeed is longer than 3 lines now.';


        $this->assertEquals($expectedOutput, $classReflection->getDocblock()->getLongDescription());

    }

    public function testDocblockTags()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(1, count($classReflection->getDocblock()->getTags()));
        $this->assertEquals(1, count($classReflection->getDocblock()->getTags('author')));

        $this->assertFalse($classReflection->getDocblock()->getTag('version'));

        $this->assertTrue($classReflection->getMethod('doSomething')->getDocblock()->hasTag('return'));

        $returnTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag', $returnTag);
        $this->assertEquals('mixed', $returnTag->getType());
    }

    public function testDocblockLines()
    {
        //$this->markTestIncomplete('Line numbers incomplete');

        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $this->assertEquals(5, $classDocblock->getStartLine());
        $this->assertEquals(15, $classDocblock->getEndLine());

    }

    public function testDocblockContents()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $expectedContents = <<<EOS
TestSampleClass5 DocBlock Short Desc

This is a long description for
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.

@author Ralph Schindler <ralph.schindler@zend.com>

EOS;

        $this->assertEquals($expectedContents, $classDocblock->getContents());

    }

    public function testToString()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $expectedString = 'DocBlock [ /* DocBlock */ ] {' . PHP_EOL
                        . PHP_EOL
                        . '  - Tags [1] {' . PHP_EOL
                        . '    DocBlock Tag [ * @author ]' . PHP_EOL
                        . '  }' . PHP_EOL
                        . '}' . PHP_EOL;

        $this->assertEquals($expectedString, (string)$classDocblock);
    }
}
