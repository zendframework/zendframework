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
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** requires */
require_once 'Zend/Reflection/File.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 */
class Zend_Reflection_DocblockTest extends PHPUnit_Framework_TestCase
{


    static protected $_sampleClassFileRequired = false;

    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }

    public function testDocblockShortDescription()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
        $this->assertEquals($classReflection->getDocblock()->getShortDescription(), 'TestSampleClass5 Docblock Short Desc');
    }

    public function testDocblockLongDescription()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
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
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

        $this->assertEquals(count($classReflection->getDocblock()->getTags()), 1);
        $this->assertEquals(count($classReflection->getDocblock()->getTags('author')), 1);

        $this->assertEquals($classReflection->getDocblock()->getTag('version'), false);

        $this->assertEquals($classReflection->getMethod('doSomething')->getDocblock()->hasTag('return'), true);

        $returnTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals(get_class($returnTag), 'Zend_Reflection_Docblock_Tag_Return');
        $this->assertEquals($returnTag->getType(), 'mixed');
    }

    public function testDocblockLines()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

        $classDocblock = $classReflection->getDocblock();

        $this->assertEquals($classDocblock->getStartLine(), 76);
        $this->assertEquals($classDocblock->getEndLine(), 86);

    }

    public function testDocblockContents()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

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
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

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
