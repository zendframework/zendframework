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

use Zend\Code\Generator\DocBlock\Tag\PropertyTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class PropertyTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new PropertyTag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testGetterAndSetterPersistValue()
    {
        $this->tag->setPropertyName('property');
        $this->assertEquals('property', $this->tag->getPropertyName());
    }

    public function testNameIsCorrect()
    {
        $this->assertEquals('property', $this->tag->getName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setPropertyName('property');
        $this->tag->setTypes('string[]');
        $this->tag->setDescription('description');
        $this->assertEquals('@property string[] $property description', $this->tag->generate());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'propertyName' => 'property',
            'types' => array('string'),
            'description' => 'description'
        ));
        $tagWithOptionsFromConstructor = new PropertyTag('property', array('string'), 'description');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }
}
