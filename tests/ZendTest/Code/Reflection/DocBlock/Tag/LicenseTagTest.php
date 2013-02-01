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

use Zend\Code\Reflection\DocBlock\Tag\LicenseTag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_DocBlock
 */
class LicenseTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LicenseTag
     */
    protected $tag;

    public function setUp()
    {
        $this->tag = new LicenseTag();
    }

    public function testParseUrl()
    {
        $this->tag->initialize('http://www.example.com');
        $this->assertEquals('license', $this->tag->getName());
        $this->assertEquals('http://www.example.com', $this->tag->getUrl());
    }

    public function testParseUrlAndLicenseName()
    {
        $this->tag->initialize('http://www.example.com Foo');
        $this->assertEquals('license', $this->tag->getName());
        $this->assertEquals('http://www.example.com', $this->tag->getUrl());
        $this->assertEquals('Foo', $this->tag->getLicenseName());
    }
}
