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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Code\Generator;

/**
 * @see Zend_CodeGenerator_Php_Class
 */

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class DocblockTagGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_CodeGenerator_Php_Docblock_Tag
     */
    protected $_tag = null;

    public function setUp()
    {
        $this->markTestIncomplete('Tag support needs refactoring');
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



//    public function testDatatypeGetterAndSetterPersistValue()
//    {
//        $this->_tag->setDatatype('Foo');
//        $this->assertEquals('Foo', $this->_tag->getDatatype());
//    }
//
//    public function testParamNameGetterAndSetterPersistValue()
//    {
//        $this->_tag->setParamName('Foo');
//        $this->assertEquals('Foo', $this->_tag->getParamName());
//    }
//
//    public function testParamProducesCorrectDocBlockLine()
//    {
//        $this->_tag->setParamName('foo');
//        $this->_tag->setDatatype('string');
//        $this->_tag->setDescription('bar bar bar');
//        $this->assertEquals('@param string $foo bar bar bar', $this->_tag->generate());
//    }

}
