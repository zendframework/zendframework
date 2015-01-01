<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag\LicenseTag;
use Zend\Code\Generator\DocBlock\TagManager;
use Zend\Code\Reflection\DocBlockReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class LicenseTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LicenseTag
     */
    protected $tag;
    /**
     * @var TagManager
     */
    protected $tagmanager;

    public function setUp()
    {
        $this->tag = new LicenseTag();
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
        $this->tag->setUrl('foo');
        $this->tag->setLicenseName('bar');

        $this->assertEquals('foo', $this->tag->getUrl());
        $this->assertEquals('bar', $this->tag->getLicenseName());
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('license', $this->tag->getName());
    }

    public function testLicenseProducesCorrectDocBlockLine()
    {
        $this->tag->setUrl('foo');
        $this->tag->setLicenseName('bar bar bar');
        $this->assertEquals('@license foo bar bar bar', $this->tag->generate());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'url' => 'foo',
            'licenseName' => 'bar',
        ));
        $tagWithOptionsFromConstructor = new LicenseTag('foo', 'bar');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }

    public function testCreatingTagFromReflection()
    {
        $docreflection = new DocBlockReflection('/** @license http://zend.com License');
        $reflectionTag = $docreflection->getTag('license');

        /** @var LicenseTag $tag */
        $tag = $this->tagmanager->createTagFromReflection($reflectionTag);
        $this->assertInstanceOf('Zend\Code\Generator\DocBlock\Tag\LicenseTag', $tag);
        $this->assertEquals('http://zend.com', $tag->getUrl());
        $this->assertEquals('License', $tag->getLicenseName());
    }
}
