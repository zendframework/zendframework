<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfRange;

class IfRangeTest extends \PHPUnit_Framework_TestCase
{

    public function testIfRangeFromStringCreatesValidIfRangeHeader()
    {
        $ifRangeHeader = IfRange::fromString('If-Range: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifRangeHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfRange', $ifRangeHeader);
    }

    public function testIfRangeGetFieldNameReturnsHeaderName()
    {
        $ifRangeHeader = new IfRange();
        $this->assertEquals('If-Range', $ifRangeHeader->getFieldName());
    }

    public function testIfRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();
        $this->assertEquals('xxx', $ifRangeHeader->getFieldValue());
    }

    public function testIfRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();

        // @todo set some values, then test output
        $this->assertEmpty('If-Range: xxx', $ifRangeHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

