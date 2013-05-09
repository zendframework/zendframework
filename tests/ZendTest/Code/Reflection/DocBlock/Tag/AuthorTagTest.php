<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\AuthorTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
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

    public function testParseName()
    {
        $this->tag->initialize('Firstname Lastname');
        $this->assertEquals('author', $this->tag->getName());
        $this->assertEquals('Firstname Lastname', $this->tag->getAuthorName());
    }

    public function testParseNameAndEmail()
    {
        $this->tag->initialize('Firstname Lastname <test@domain.fr>');
        $this->assertEquals('author', $this->tag->getName());
        $this->assertEquals('Firstname Lastname', $this->tag->getAuthorName());
        $this->assertEquals('test@domain.fr', $this->tag->getAuthorEmail());
    }
}
