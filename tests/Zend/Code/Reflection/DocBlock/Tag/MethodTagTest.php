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

namespace ZendTest\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\MethodTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $tag->initialize('string test()');
        $this->assertEquals('method', $tag->getName());
        $this->assertEquals('test()', $tag->getMethodName());
        $this->assertFalse($tag->isStatic());
        $this->assertEquals('string', $tag->getReturnType());
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