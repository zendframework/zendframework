<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\DocBlock\Tag;

use ZendTest\Code\Generator\TestAsset\TypeableTag;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class TypableTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeableTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new TypeableTag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testGetterAndSetterPersistValue()
    {
        $this->tag->setTypes(array('string', 'null'));
        $this->tag->setDescription('Description');
        $this->assertEquals(array('string', 'null'), $this->tag->getTypes());
        $this->assertEquals('Description', $this->tag->getDescription());
    }

    public function testGetterForTypesAsStringWithSingleType()
    {
        $this->tag->setTypes(array('string'));
        $this->assertEquals('string', $this->tag->getTypesAsString());
    }

    public function testGetterForTypesAsStringWithSingleTypeAndDelimiter()
    {
        $this->tag->setTypes(array('string'));
        $this->assertEquals('string', $this->tag->getTypesAsString('/'));
    }

    public function testGetterForTypesAsStringWithMultipleTypes()
    {
        $this->tag->setTypes(array('string', 'null'));
        $this->assertEquals('string|null', $this->tag->getTypesAsString());
    }

    public function testGetterForTypesAsStringWithMultipleTypesAndDelimiter()
    {
        $this->tag->setTypes(array('string', 'null'));
        $this->assertEquals('string/null', $this->tag->getTypesAsString('/'));
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'types' => array('string', 'null'),
            'description' => 'description',
        ));
        $tagWithOptionsFromConstructor = new TypeableTag(array('string', 'null'), 'description');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }
}
