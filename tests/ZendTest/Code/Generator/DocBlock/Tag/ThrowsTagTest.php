<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\ThrowsTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ThrowsTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ThrowsTag
     */
    protected $tag;
    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new ThrowsTag();
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
        $this->assertEquals('throws', $this->tag->getName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setTypes('Exception\\MyException');
        $this->tag->setDescription('description');
        $this->assertEquals('@throws Exception\\MyException description', $this->tag->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @throws Exception\Invalid description');
        $reflectionTag = $docreflection->getTag('throws');

        /** @var ThrowsTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\ThrowsTag', $tag);
        $this->assertEquals('description', $tag->getDescription());
        $this->assertEquals('Exception\Invalid', $tag->getTypesAsString());
    }
}
