<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentMD5;

class ContentMD5Test extends \PHPUnit_Framework_TestCase
{

    public function testContentMD5FromStringCreatesValidContentMD5Header()
    {
        $contentMD5Header = ContentMD5::fromString('Content-MD5: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentMD5Header);
        $this->assertInstanceOf('Zend\Http\Header\ContentMD5', $contentMD5Header);
    }

    public function testContentMD5GetFieldNameReturnsHeaderName()
    {
        $contentMD5Header = new ContentMD5();
        $this->assertEquals('Content-MD5', $contentMD5Header->getFieldName());
    }

    public function testContentMD5GetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentMD5 needs to be completed');

        $contentMD5Header = new ContentMD5();
        $this->assertEquals('xxx', $contentMD5Header->getFieldValue());
    }

    public function testContentMD5ToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentMD5 needs to be completed');

        $contentMD5Header = new ContentMD5();

        // @todo set some values, then test output
        $this->assertEmpty('Content-MD5: xxx', $contentMD5Header->toString());
    }

    /** Implmentation specific tests here */

}
