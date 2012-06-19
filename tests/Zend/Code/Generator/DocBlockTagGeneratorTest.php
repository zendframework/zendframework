<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\DocBlock\Tag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class DocBlockTagGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Tag */
    protected $tag;

    public function setUp()
    {
        $this->tag = new Tag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testCanPassNameToConstructor()
    {
        $tag = new Tag(array('name' => 'Foo'));
        $this->assertEquals('Foo', $tag->getName());
    }

    public function testCanPassDescriptionToConstructor()
    {
        $tag = new Tag(array('description' => 'Foo'));
        $this->assertEquals('Foo', $tag->getDescription());
    }

    public function testNameGetterAndSetterPersistValue()
    {
        $this->tag->setName('Foo');
        $this->assertEquals('Foo', $this->tag->getName());
    }

    public function testDescriptionGetterAndSetterPersistValue()
    {
        $this->tag->setDescription('Foo foo foo');
        $this->assertEquals('Foo foo foo', $this->tag->getDescription());
    }

    public function testDatatypeGetterAndSetterPersistValue()
    {
        $this->tag = new Tag\ParamTag();
        $this->tag->setDatatype('Foo');
        $this->assertEquals('Foo', $this->tag->getDatatype());
    }

    public function testParamNameGetterAndSetterPersistValue()
    {
        $this->tag = new Tag\ParamTag();
        $this->tag->setParamName('Foo');
        $this->assertEquals('Foo', $this->tag->getParamName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag = new Tag\ParamTag();
        $this->tag->setParamName('foo');
        $this->tag->setDatatype('string');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@param string $foo bar bar bar', $this->tag->generate());
    }

    public function testParamProducesCorrectDocBlockTag()
    {
        $this->tag->setName('foo');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@foo bar bar bar', $this->tag->generate());
    }
}
