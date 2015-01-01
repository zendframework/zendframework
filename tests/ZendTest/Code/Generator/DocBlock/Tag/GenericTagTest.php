<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\GenericTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class GenericTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericTag
     */
    protected $tag;
    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new GenericTag();
        $this->tagmanager = new TagManager();
        $this->tagmanager->initializeDefaultTags();
    }

    public function tearDown()
    {
        $this->tag = null;
        $this->tagmanager = null;
    }

    public function testGetterAndSetterPersistValue()
    {
        $this->tag->setName('var');
        $this->tag->setContent('string');
        $this->assertEquals('var', $this->tag->getName());
        $this->assertEquals('string', $this->tag->getContent());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setName('var');
        $this->tag->setContent('string');
        $this->assertEquals('@var string', $this->tag->generate());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'name' => 'var',
            'content' => 'string',
        ));
        $tagWithOptionsFromConstructor = new GenericTag('var', 'string');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @var string');
        $reflectionTag = $docreflection->getTag('var');

        /** @var GenericTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\GenericTag', $tag);
        $this->assertEquals('var', $tag->getName());
        $this->assertEquals('string', $tag->getContent());
    }
}
