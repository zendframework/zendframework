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

use Zend\Http\Header\ContentType;

class ContentTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testContentTypeFromStringCreatesValidContentTypeHeader()
    {
        $contentTypeHeader = ContentType::fromString('Content-Type: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentTypeHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentType', $contentTypeHeader);
    }

    public function testContentTypeGetFieldNameReturnsHeaderName()
    {
        $contentTypeHeader = new ContentType();
        $this->assertEquals('Content-Type', $contentTypeHeader->getFieldName());
    }

    public function testContentTypeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentType needs to be completed');

        $contentTypeHeader = new ContentType();
        $this->assertEquals('xxx', $contentTypeHeader->getFieldValue());
    }

    public function testContentTypeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentType needs to be completed');

        $contentTypeHeader = new ContentType();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Type: xxx', $contentTypeHeader->toString());
    }

    /** Implmentation specific tests here */

}
