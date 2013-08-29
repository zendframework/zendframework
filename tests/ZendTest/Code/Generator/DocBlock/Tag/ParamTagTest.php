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

use Zend\Code\Generator\DocBlock\Tag\ParamTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ParamTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParamTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new ParamTag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testGetterAndSetterPersistValue()
    {
        $this->tag->setVariableName('Foo');
        $this->assertEquals('Foo', $this->tag->getVariableName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setVariableName('foo');
        $this->tag->setTypes('string');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@param string $foo bar bar bar', $this->tag->generate());
    }

    public function testConstructorWithOptions()
    {
        $this->tag->setOptions(array(
            'types' => 'string',
            'variableName' => 'foo',
        ));
        $tagWithOptionsFromConstructor = new ParamTag('foo', 'string');
        $this->assertEquals($this->tag->generate(), $tagWithOptionsFromConstructor->generate());
    }
}
