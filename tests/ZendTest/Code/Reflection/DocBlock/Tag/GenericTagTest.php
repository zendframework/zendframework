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

use Zend\Code\Reflection\DocBlock\Tag\GenericTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class GenericTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF2-146
     */
    public function testParse()
    {
        $tag = new GenericTag();
        $tag->initialize('baz zab');
        $this->assertEquals('baz', $tag->returnValue(0));
        $this->assertEquals('zab', $tag->returnValue(1));
    }
}
