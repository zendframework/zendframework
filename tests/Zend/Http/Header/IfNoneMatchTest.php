<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfNoneMatch;

class IfNoneMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testIfNoneMatchFromStringCreatesValidIfNoneMatchHeader()
    {
        $ifNoneMatchHeader = IfNoneMatch::fromString('If-None-Match: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifNoneMatchHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfNoneMatch', $ifNoneMatchHeader);
    }

    public function testIfNoneMatchGetFieldNameReturnsHeaderName()
    {
        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('If-None-Match', $ifNoneMatchHeader->getFieldName());
    }

    public function testIfNoneMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('xxx', $ifNoneMatchHeader->getFieldValue());
    }

    public function testIfNoneMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-None-Match: xxx', $ifNoneMatchHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

