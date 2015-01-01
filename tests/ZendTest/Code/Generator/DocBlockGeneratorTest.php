<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group      Zend_Code_Generator
 * @group      Zend_Code_Generator_Php
 */
class DocBlockGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocBlockGenerator
     */
    protected $docBlockGenerator;

    /**
     * @var DocBlockGenerator
     */
    protected $reflectionDocBlockGenerator;

    protected function setUp()
    {
        $this->docBlockGenerator = $this->docBlockGenerator = new DocBlockGenerator();
        $reflectionDocBlock = new DocBlockReflection(
            '/**
 * Short Description
 * Long Description
 * @param string $foo comment
 * @author Zend <zend@zend.com>
 * @license http://license The License
 * @return int
 */'
        );

        $this->reflectionDocBlockGenerator = DocBlockGenerator::fromReflection($reflectionDocBlock);
    }

    public function testCanPassTagsToConstructor()
    {
        $docBlockGenerator = new DocBlockGenerator(null, null, array(
            array('name' => 'foo')
        ));

        $tags = $docBlockGenerator->getTags();
        $this->assertCount(1, $tags);

        $this->assertEquals('foo', $tags[0]->getName());
    }

    public function testShortDescriptionGetterAndSetter()
    {
        $this->docBlockGenerator->setShortDescription('Short Description');
        $this->assertEquals('Short Description', $this->docBlockGenerator->getShortDescription());
    }

    public function testLongDescriptionGetterAndSetter()
    {
        $this->docBlockGenerator->setLongDescription('Long Description');
        $this->assertEquals('Long Description', $this->docBlockGenerator->getLongDescription());
    }

    public function testTagGettersAndSetters()
    {
        $paramTag = new Tag\ParamTag();
        $paramTag->setDatatype('string');

        $returnTag = new Tag\ReturnTag();
        $returnTag->setDatatype('int');

        $this->docBlockGenerator->setTag(array('name' => 'blah'));
        $this->docBlockGenerator->setTag($paramTag);
        $this->docBlockGenerator->setTag($returnTag);
        $this->assertEquals(3, count($this->docBlockGenerator->getTags()));

        $target = <<<EOS
/**
 * @blah
 * @param string
 * @return int
 */

EOS;

        $this->assertEquals($target, $this->docBlockGenerator->generate());
    }

    public function testGenerationOfDocBlock()
    {
        $this->docBlockGenerator->setShortDescription('@var Foo this is foo bar');

        $expected = '/**' . DocBlockGenerator::LINE_FEED . ' * @var Foo this is foo bar'
            . DocBlockGenerator::LINE_FEED . ' */' . DocBlockGenerator::LINE_FEED;
        $this->assertEquals($expected, $this->docBlockGenerator->generate());
    }

    public function testCreateFromArray()
    {
        $docBlock = DocBlockGenerator::fromArray(array(
            'shortdescription' => 'foo',
            'longdescription'  => 'bar',
            'tags' => array(
                array(
                    'name'        => 'foo',
                    'description' => 'bar',
                )
            ),
        ));

        $this->assertEquals('foo', $docBlock->getShortDescription());
        $this->assertEquals('bar', $docBlock->getLongDescription());
        $this->assertCount(1, $docBlock->getTags());
    }

    /**
     * @group #3753
     */
    public function testGenerateWordWrapIsEnabledByDefault()
    {
        $largeStr = '@var This is a very large string that will be wrapped if it contains more than 80 characters';
        $this->docBlockGenerator->setLongDescription($largeStr);

        $expected = '/**' . DocBlockGenerator::LINE_FEED
            . ' * @var This is a very large string that will be wrapped if it contains more than'
            . DocBlockGenerator::LINE_FEED.' * 80 characters'. DocBlockGenerator::LINE_FEED
            . ' */' . DocBlockGenerator::LINE_FEED;
        $this->assertEquals($expected, $this->docBlockGenerator->generate());
    }

    /**
     * @group #3753
     */
    public function testGenerateWithWordWrapDisabled()
    {
        $largeStr = '@var This is a very large string that will not be wrapped if it contains more than 80 characters';
        $this->docBlockGenerator->setLongDescription($largeStr);
        $this->docBlockGenerator->setWordWrap(false);

        $expected = '/**' . DocBlockGenerator::LINE_FEED
            . ' * @var This is a very large string that will not be wrapped if it contains more than'
            . ' 80 characters'. DocBlockGenerator::LINE_FEED . ' */' . DocBlockGenerator::LINE_FEED;
        $this->assertEquals($expected, $this->docBlockGenerator->generate());
    }

    public function testDocBlockFromRefelectionLongDescription()
    {
        $this->assertEquals('Long Description', $this->reflectionDocBlockGenerator->getLongDescription());
    }

    public function testDocBlockFromRefelectionShortDescription()
    {
        $this->assertEquals('Short Description', $this->reflectionDocBlockGenerator->getShortDescription());
    }

    public function testDocBlockFromRefelectionTagsCount()
    {
        $this->assertCount(4, $this->reflectionDocBlockGenerator->getTags());
    }

    /**
     * @depends testDocBlockFromRefelectionTagsCount
     */
    public function testDocBlockFromRefelectionParamTag()
    {
        $tags = $this->reflectionDocBlockGenerator->getTags();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\ParamTag', $tags[0]);
    }

    /**
     * @depends testDocBlockFromRefelectionTagsCount
     */
    public function testDocBlockFromRefelectionAuthorTag()
    {
        $tags = $this->reflectionDocBlockGenerator->getTags();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\AuthorTag', $tags[1]);
    }

    /**
     * @depends testDocBlockFromRefelectionTagsCount
     */
    public function testDocBlockFromRefelectionLicenseTag()
    {
        $tags = $this->reflectionDocBlockGenerator->getTags();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\LicenseTag', $tags[2]);
    }

    /**
     * @depends testDocBlockFromRefelectionTagsCount
     */
    public function testDocBlockFromRefelectionReturnTag()
    {
        $tags = $this->reflectionDocBlockGenerator->getTags();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\ReturnTag', $tags[3]);
    }
}
