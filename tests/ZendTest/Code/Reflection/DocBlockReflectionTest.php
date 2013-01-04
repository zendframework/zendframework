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

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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

    public function testTabbedDocBlockTags()
    {
        $classReflection = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass10');

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

    public function testFunctionDocBlockTags()
    {
        $docblock = '
    /**
     * Method ShortDescription
     *
     * @param int $one Description for one
     * @param int[] Description for two
     * @param string|null $three Description for three
     *                      which spans multiple lines
     * @return int[]|null Description
     * @throws Exception
     */
';

        $docblockReflection = new DocBlockReflection($docblock);

        $paramTags = $docblockReflection->getTags('param');

        $this->assertEquals(5, count($docblockReflection->getTags()));
        $this->assertEquals(3, count($paramTags));
        $this->assertEquals(1, count($docblockReflection->getTags('return')));
        $this->assertEquals(1, count($docblockReflection->getTags('throws')));

        $returnTag = $docblockReflection->getTag('return');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\ReturnTag', $returnTag);
        $this->assertEquals('int[]', $returnTag->getType());
        $this->assertEquals(array('int[]', 'null'), $returnTag->getTypes());
        $this->assertEquals('Description', $returnTag->getDescription());

        $throwsTag = $docblockReflection->getTag('throws');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\ThrowsTag', $throwsTag);
        $this->assertEquals('Exception', $throwsTag->getType());

        $paramTag = $paramTags[0];
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\ParamTag', $paramTag);
        $this->assertEquals('int', $paramTag->getType());

        $paramTag = $paramTags[1];
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\ParamTag', $paramTag);
        $this->assertEquals('int[]', $paramTag->getType());

        $paramTag = $paramTags[2];
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlock\Tag\ParamTag', $paramTag);
        $this->assertEquals('string', $paramTag->getType());
        $this->assertEquals(array('string', 'null'), $paramTag->getTypes());
    }
}
