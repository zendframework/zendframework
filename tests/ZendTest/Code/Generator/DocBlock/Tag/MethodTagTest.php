<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\MethodTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class MethodTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MethodTag
     */
    protected $tag;

    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new MethodTag();
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
        $this->tag->setIsStatic(true);
        $this->tag->setMethodName('method');
        $this->assertEquals(true, $this->tag->isStatic());
        $this->assertEquals('method', $this->tag->getMethodName());
    }

    public function testGetterForMethodNameTrimsCorrectly()
    {
        $this->tag->setMethodName('()method()');
        $this->assertEquals('()method', $this->tag->getMethodName());
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('method', $this->tag->getName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setIsStatic(true);
        $this->tag->setMethodName('method');
        $this->tag->setTypes('int');
        $this->tag->setDescription('method(string $a)');
        $this->assertEquals('@method static int method() method(string $a)', $this->tag->generate());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'isStatic' => true,
            'methodName' => 'method',
            'types' => array('string'),
            'description' => 'description'
        ));
        $tagWithOptionsFromConstructor = new MethodTag('method', array('string'), 'description', true);
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @method static int method() method(int $a)');
        $reflectionTag = $docreflection->getTag('method');

        /** @var MethodTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\MethodTag', $tag);
        $this->assertEquals(true, $tag->isStatic());
        $this->assertEquals('int', $tag->getTypesAsString());
        $this->assertEquals('method', $tag->getMethodName());
        $this->assertEquals('method(int $a)', $tag->getDescription());
    }
}
