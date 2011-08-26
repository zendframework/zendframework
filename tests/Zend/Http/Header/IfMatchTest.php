<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfMatch;

class IfMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testIfMatchFromStringCreatesValidIfMatchHeader()
    {
        $ifMatchHeader = IfMatch::fromString('If-Match: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $ifMatchHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfMatch', $ifMatchHeader);
    }

    public function testIfMatchGetFieldNameReturnsHeaderName()
    {
        $ifMatchHeader = new IfMatch();
        $this->assertEquals('If-Match', $ifMatchHeader->getFieldName());
    }

    public function testIfMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();
        $this->assertEquals('xxx', $ifMatchHeader->getFieldValue());
    }

    public function testIfMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-Match: xxx', $ifMatchHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

