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
 * @namespace
 */
namespace ZendTest\Reflection;
use Zend\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 */
class ReflectionDocblockTest extends \PHPUnit_Framework_TestCase
{
    public function testDocblockShortDescription()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');
        $this->assertEquals($classReflection->getDocblock()->getShortDescription(), 'TestSampleClass5 Docblock Short Desc');
    }

    public function testDocblockLongDescription()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');
        $expectedOutput =<<<EOS
This is a long description for
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.
EOS;

        $this->assertEquals($classReflection->getDocblock()->getLongDescription(), $expectedOutput);

    }

    public function testDocblockTags()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(count($classReflection->getDocblock()->getTags()), 1);
        $this->assertEquals(count($classReflection->getDocblock()->getTags('author')), 1);

        $this->assertEquals($classReflection->getDocblock()->getTag('version'), false);

        $this->assertEquals($classReflection->getMethod('doSomething')->getDocblock()->hasTag('return'), true);

        $returnTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals(get_class($returnTag), 'Zend\Reflection\ReflectionDocblockTag');
        $this->assertEquals($returnTag->getType(), 'mixed');
    }

    public function testDocblockLines()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $this->assertEquals($classDocblock->getStartLine(), 5);
        $this->assertEquals($classDocblock->getEndLine(), 15);

    }

    public function testDocblockContents()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $expectedContents = <<<EOS
TestSampleClass5 Docblock Short Desc

This is a long description for
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.

@author Ralph Schindler <ralph.schindler@zend.com>

EOS;

        $this->assertEquals($classDocblock->getContents(), $expectedContents);

    }

    public function testToString()
    {
        $classReflection = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $expectedString = 'Docblock [ /* Docblock */ ] {' . PHP_EOL
                        . PHP_EOL
                        . '  - Tags [1] {' . PHP_EOL
                        . '    Docblock Tag [ * @author ]' . PHP_EOL
                        . '  }' . PHP_EOL
                        . '}' . PHP_EOL;

        $this->assertEquals($expectedString, (string)$classDocblock);
    }
}
