<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\ThrowsTag;

/**
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class ThrowsTagTest extends \PHPUnit_Framework_TestCase
{
    public function testAllCharactersFromTypenameAreSupported()
    {
        $tag = new ThrowsTag();
        $tag->initialize('\\Logic_2_Exception');
        $this->assertEquals(array('\\Logic_2_Exception'), $tag->getTypes());
    }

    public function testSingleTypeWithDescription()
    {
        $tag = new ThrowsTag();
        $tag->initialize('LogicException The Exception');
        $this->assertEquals(array('LogicException'), $tag->getTypes());
        $this->assertEquals('The Exception', $tag->getDescription());
    }

    public function testSingleTypeWithoutDescription()
    {
        $tag = new ThrowsTag();
        $tag->initialize('LogicException');
        $this->assertEquals(array('LogicException'), $tag->getTypes());
        $this->assertNull($tag->getDescription());
    }

    public function testMultipleTypesWithoutDescription()
    {
        $tag = new ThrowsTag();
        $tag->initialize('LogicException|RuntimeException');
        $this->assertEquals(array('LogicException', 'RuntimeException'), $tag->getTypes());
        $this->assertNull($tag->getDescription());
    }

    public function testMultipleTypesWithDescription()
    {
        $tag = new ThrowsTag();
        $tag->initialize('LogicException|RuntimeException The Exception');
        $this->assertEquals(array('LogicException', 'RuntimeException'), $tag->getTypes());
        $this->assertEquals('The Exception', $tag->getDescription());
    }
}
