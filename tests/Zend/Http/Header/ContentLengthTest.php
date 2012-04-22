<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLength;

class ContentLengthTest extends \PHPUnit_Framework_TestCase
{

    public function testContentLengthFromStringCreatesValidContentLengthHeader()
    {
        $contentLengthHeader = ContentLength::fromString('Content-Length: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentLengthHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLength', $contentLengthHeader);
    }

    public function testContentLengthGetFieldNameReturnsHeaderName()
    {
        $contentLengthHeader = new ContentLength();
        $this->assertEquals('Content-Length', $contentLengthHeader->getFieldName());
    }

    public function testContentLengthGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();
        $this->assertEquals('xxx', $contentLengthHeader->getFieldValue());
    }

    public function testContentLengthToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Length: xxx', $contentLengthHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

