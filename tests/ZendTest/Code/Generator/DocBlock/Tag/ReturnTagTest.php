<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ReturnTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReturnTag
     */
    protected $tag;
    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new ReturnTag();
        $this->tagmanager = new TagManager();
        $this->tagmanager->initializeDefaultTags();
    }

    public function tearDown()
    {
        $this->tag = null;
        $this->tagmanager = null;
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('return', $this->tag->getName());
    }

    public function testReturnProducesCorrectDocBlockLine()
    {
        $this->tag->setTypes('string|int');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@return string|int bar bar bar', $this->tag->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @return int The return');
        $reflectionTag = $docreflection->getTag('return');

        /** @var ReturnTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\ReturnTag', $tag);
        $this->assertEquals('The return', $tag->getDescription());
        $this->assertEquals('int', $tag->getTypesAsString());
    }
}
