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

use Zend\Code\Generator\DocBlock\Tag\AuthorTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class AuthorTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new AuthorTag();
    }

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testDatatypeGetterAndSetterPersistValue()
    {
        $this->tag->setDatatype('Foo');
        $this->assertEquals('Foo', $this->tag->getDatatype());
    }

    public function testParamNameGetterAndSetterPersistValue()
    {
        $this->tag->setParamName('Foo');
        $this->assertEquals('Foo', $this->tag->getParamName());
    }

    public function testParamProducesCorrectDocBlockLine()
    {
        $this->tag->setParamName('foo');
        $this->tag->setDatatype('string');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@param string $foo bar bar bar', $this->tag->generate());
    }
}
