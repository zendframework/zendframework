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

use Zend\Code\Generator\DocBlock\Tag\LicenseTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
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

    public function tearDown()
    {
        $this->tag = null;
    }

    public function testUrlGetterAndSetterPersistValue()
    {
        $this->tag->setUrl('foo');
        $this->tag->setLicenseName('bar');

        $this->assertEquals('foo', $this->tag->getUrl());
        $this->assertEquals('bar', $this->tag->getLicenseName());
    }

    public function testLicenseProducesCorrectDocBlockLine()
    {
        $this->tag->setUrl('foo');
        $this->tag->setLicenseName('bar bar bar');
        $this->assertEquals('@license foo bar bar bar', $this->tag->generate());
    }
}
