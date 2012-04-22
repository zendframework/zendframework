<?php

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

