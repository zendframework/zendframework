<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\CacheControl;

class CacheControlTest extends \PHPUnit_Framework_TestCase
{

    public function testCacheControlFromStringCreatesValidCacheControlHeader()
    {
        $cacheControlHeader = CacheControl::fromString('Cache-Control: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $cacheControlHeader);
        $this->assertInstanceOf('Zend\Http\Header\CacheControl', $cacheControlHeader);
    }

    public function testCacheControlGetFieldNameReturnsHeaderName()
    {
        $cacheControlHeader = new CacheControl();
        $this->assertEquals('Cache-Control', $cacheControlHeader->getFieldName());
    }

    public function testCacheControlGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();
        $this->assertEquals('xxx', $cacheControlHeader->getFieldValue());
    }

    public function testCacheControlToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();

        // @todo set some values, then test output
        $this->assertEmpty('Cache-Control: xxx', $cacheControlHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

