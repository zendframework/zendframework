<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentRange;

class ContentRangeTest extends \PHPUnit_Framework_TestCase
{

    public function testContentRangeFromStringCreatesValidContentRangeHeader()
    {
        $contentRangeHeader = ContentRange::fromString('Content-Range: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $contentRangeHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentRange', $contentRangeHeader);
    }

    public function testContentRangeGetFieldNameReturnsHeaderName()
    {
        $contentRangeHeader = new ContentRange();
        $this->assertEquals('Content-Range', $contentRangeHeader->getFieldName());
    }

    public function testContentRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();
        $this->assertEquals('xxx', $contentRangeHeader->getFieldValue());
    }

    public function testContentRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Range: xxx', $contentRangeHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

