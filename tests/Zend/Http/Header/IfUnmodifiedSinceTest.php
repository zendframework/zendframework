<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfUnmodifiedSince;

class IfUnmodifiedSinceTest extends \PHPUnit_Framework_TestCase
{

    public function testIfUnmodifiedSinceFromStringCreatesValidIfUnmodifiedSinceHeader()
    {
        $ifUnmodifiedSinceHeader = IfUnmodifiedSince::fromString('If-Unmodified-Since: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $ifUnmodifiedSinceHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfUnmodifiedSince', $ifUnmodifiedSinceHeader);
    }

    public function testIfUnmodifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $this->assertEquals('If-Unmodified-Since', $ifUnmodifiedSinceHeader->getFieldName());
    }

    public function testIfUnmodifiedSinceGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfUnmodifiedSince needs to be completed');

        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $this->assertEquals('xxx', $ifUnmodifiedSinceHeader->getFieldValue());
    }

    public function testIfUnmodifiedSinceToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfUnmodifiedSince needs to be completed');

        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();

        // @todo set some values, then test output
        $this->assertEmpty('If-Unmodified-Since: xxx', $ifUnmodifiedSinceHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

