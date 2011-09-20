<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Etag;

class EtagTest extends \PHPUnit_Framework_TestCase
{

    public function testEtagFromStringCreatesValidEtagHeader()
    {
        $etagHeader = Etag::fromString('Etag: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $etagHeader);
        $this->assertInstanceOf('Zend\Http\Header\Etag', $etagHeader);
    }

    public function testEtagGetFieldNameReturnsHeaderName()
    {
        $etagHeader = new Etag();
        $this->assertEquals('Etag', $etagHeader->getFieldName());
    }

    public function testEtagGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();
        $this->assertEquals('xxx', $etagHeader->getFieldValue());
    }

    public function testEtagToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();

        // @todo set some values, then test output
        $this->assertEmpty('Etag: xxx', $etagHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

