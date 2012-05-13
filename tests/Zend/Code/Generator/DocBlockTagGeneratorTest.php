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
    /**
     * @var Tag
     */
    protected $_tag = null;

    public function setUp()
    {
        $this->_tag = new Tag();
    }

    public function tearDown()
    {
        $this->_tag = null;
    }

    public function testNameGetterAndSetterPersistValue()
    {
        $this->_tag->setName('Foo');
        $this->assertEquals('Foo', $this->_tag->getName());
    }

    public function testDescriptionGetterAndSetterPersistValue()
    {
        $this->_tag->setDescription('Foo foo foo');
        $this->assertEquals('Foo foo foo', $this->_tag->getDescription());
    }

    public function testDatatypeGetterAndSetterPersistValue()
    {
        $this->markTestIncomplete('Docbook tag is not completed');
        $this->_tag->setDatatype('Foo');
        $this->assertEquals('Foo', $this->_tag->getDatatype());
    }

    public function testParamNameGetterAndSetterPersistValue()
    {
        $this->markTestIncomplete('Docbook tag is not completed');
        $this->_tag->setParamName('Foo');
        $this->assertEquals('Foo', $this->_tag->getParamName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->markTestIncomplete('Docbook tag is not completed');
        $this->_tag->setParamName('foo');
        $this->_tag->setDatatype('string');
        $this->_tag->setDescription('bar bar bar');
        $this->assertEquals('@param string $foo bar bar bar', $this->_tag->generate());
    }

    public function testParamProducesCorrectDocBlockTag()
    {
        $this->_tag->setName('foo');
        $this->_tag->setDescription('bar bar bar');
        $this->assertEquals('@foo bar bar bar', $this->_tag->generate());
    }
}
