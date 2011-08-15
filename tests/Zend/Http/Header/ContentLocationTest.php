<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLocation;

class ContentLocationTest extends \PHPUnit_Framework_TestCase
{

    public function testContentLocationFromStringCreatesValidContentLocationHeader()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $contentLocationHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLocation', $contentLocationHeader);
    }

    public function testContentLocationGetFieldNameReturnsHeaderName()
    {
        $contentLocationHeader = new ContentLocation();
        $this->assertEquals('Content-Location', $contentLocationHeader->getFieldName());
    }

    public function testContentLocationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLocation needs to be completed');

        $contentLocationHeader = new ContentLocation();
        $this->assertEquals('xxx', $contentLocationHeader->getFieldValue());
    }

    public function testContentLocationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLocation needs to be completed');

        $contentLocationHeader = new ContentLocation();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Location: xxx', $contentLocationHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

