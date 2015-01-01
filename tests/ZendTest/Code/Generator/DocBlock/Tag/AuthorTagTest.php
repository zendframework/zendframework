<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\AuthorTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class AuthorTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorTag
     */
    protected $tag;

    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new AuthorTag();
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
        $this->tag->setAuthorName('Foo');
        $this->tag->setAuthorEmail('Bar');
        $this->assertEquals('Foo', $this->tag->getAuthorName());
        $this->assertEquals('Bar', $this->tag->getAuthorEmail());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setAuthorName('foo');
        $this->tag->setAuthorEmail('string');
        $this->assertEquals('@author foo <string>', $this->tag->generate());
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('author', $this->tag->getName());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'authorEmail' => 'string',
            'authorName' => 'foo',
        ));
        $tagWithOptionsFromConstructor = new AuthorTag('foo', 'string');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @author Mister Miller <mister.miller@zend.com>');
        $reflectionTag = $docreflection->getTag('author');

        /** @var AuthorTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\AuthorTag', $tag);
        $this->assertEquals('Mister Miller', $tag->getAuthorName());
        $this->assertEquals('mister.miller@zend.com', $tag->getAuthorEmail());
    }
}
