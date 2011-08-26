<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfModifiedSince;

class IfModifiedSinceTest extends \PHPUnit_Framework_TestCase
{

    public function testIfModifiedSinceFromStringCreatesValidIfModifiedSinceHeader()
    {
        $ifModifiedSinceHeader = IfModifiedSince::fromString('If-Modified-Since: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $ifModifiedSinceHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfModifiedSince', $ifModifiedSinceHeader);
    }

    public function testIfModifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $this->assertEquals('If-Modified-Since', $ifModifiedSinceHeader->getFieldName());
    }

    public function testIfModifiedSinceGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfModifiedSince needs to be completed');

        $ifModifiedSinceHeader = new IfModifiedSince();
        $this->assertEquals('xxx', $ifModifiedSinceHeader->getFieldValue());
    }

    public function testIfModifiedSinceToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfModifiedSince needs to be completed');

        $ifModifiedSinceHeader = new IfModifiedSince();

        // @todo set some values, then test output
        $this->assertEmpty('If-Modified-Since: xxx', $ifModifiedSinceHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

