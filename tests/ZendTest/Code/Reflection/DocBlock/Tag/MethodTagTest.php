<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\MethodTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class MethodTagTest extends \PHPUnit_Framework_TestCase
{
    public function testParseName()
    {
        $tag = new MethodTag();
        $tag->initialize('test()');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertFalse($tag->isStatic());
        $this->assertNull($tag->getReturnType());
        $this->assertNull($tag->getDescription());
    }

    public function testParseNameAndType()
    {
        $tag = new MethodTag();
        $tag->initialize('string|null test()');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertFalse($tag->isStatic());
        $this->assertEquals('string', $tag->getReturnType());
        $this->assertEquals(array('string', 'null'), $tag->getTypes());
        $this->assertNull($tag->getDescription());
    }

    public function testParseNameAndStatic()
    {
        $tag = new MethodTag();
        $tag->initialize('static test()');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertTrue($tag->isStatic());
        $this->assertNull($tag->getReturnType());
        $this->assertNull($tag->getDescription());
    }

    public function testParseNameAndStaticAndDescription()
    {
        $tag = new MethodTag();
        $tag->initialize('static test() I\'m test method');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertTrue($tag->isStatic());
        $this->assertNull($tag->getReturnType());
        $this->assertEquals('I\'m test method', $tag->getDescription());
    }

    public function testParseNameAndTypeAndStaticAndDescription()
    {
        $tag = new MethodTag();
        $tag->initialize('static string test() I\'m test method');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertTrue($tag->isStatic());
        $this->assertEquals('string', $tag->getReturnType());
        $this->assertEquals('I\'m test method', $tag->getDescription());
    }
}
