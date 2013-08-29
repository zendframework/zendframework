<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\DocBlock\Tag\ThrowsTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ThrowsTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ThrowsTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new ThrowsTag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('throws', $this->tag->getName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setTypes('Exception\\MyException');
        $this->tag->setDescription('description');
        $this->assertEquals('@throws Exception\\MyException description', $this->tag->generate());
    }
}
