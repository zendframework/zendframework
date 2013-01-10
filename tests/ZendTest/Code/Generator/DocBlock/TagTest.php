<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Generator\DocBlock;

use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\DocBlock\Tag\LicenseTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Tag
     */
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

    public function testCanGenerateLicenseTag()
    {
        $tag = new LicenseTag(array(
            'url'         => 'http://test.license.com',
            'description' => 'Test License',
        ));
        $this->assertEquals(
            '@license http://test.license.com Test License',
            $tag->generate()
        );
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

    public function testParamProducesCorrectDocBlockTag()
    {
        $this->tag->setName('foo');
        $this->tag->setDescription('bar bar bar');
        $this->assertEquals('@foo bar bar bar', $this->tag->generate());
    }
}
