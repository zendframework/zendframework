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

use Zend\Code\Reflection\DocBlock\Tag\PropertyTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class PropertyTagTest extends \PHPUnit_Framework_TestCase
{
    public function testParseName()
    {
        $tag = new PropertyTag();
        $tag->initialize('$test');
        $this->assertEquals('property', $tag->getName());
        $this->assertEquals('$test', $tag->getPropertyName());
        $this->assertNull($tag->getType());
        $this->assertNull($tag->getDescription());
    }

    public function testParseTypeAndName()
    {
        $tag = new PropertyTag();
        $tag->initialize('string|null $test');
        $this->assertEquals('$test', $tag->getPropertyName());
        $this->assertNull($tag->getDescription());
        $this->assertEquals('string', $tag->getType());
        $this->assertEquals(array('string', 'null'), $tag->getTypes());
    }

    public function testParseNameAndDescription()
    {
        $tag = new PropertyTag();
        $tag->initialize('$test I\'m test property');
        $this->assertEquals('$test', $tag->getPropertyName());
        $this->assertNull($tag->getType());
        $this->assertEquals('I\'m test property', $tag->getDescription());
    }

    public function testParseTypeAndNameAndDescription()
    {
        $tag = new PropertyTag();
        $tag->initialize('string $test I\'m test property');
        $this->assertEquals('$test', $tag->getPropertyName());
        $this->assertEquals('string', $tag->getType());
        $this->assertEquals('I\'m test property', $tag->getDescription());
    }
}
