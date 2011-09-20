<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentDisposition;

class ContentDispositionTest extends \PHPUnit_Framework_TestCase
{

    public function testContentDispositionFromStringCreatesValidContentDispositionHeader()
    {
        $contentDispositionHeader = ContentDisposition::fromString('Content-Disposition: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $contentDispositionHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentDisposition', $contentDispositionHeader);
    }

    public function testContentDispositionGetFieldNameReturnsHeaderName()
    {
        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('Content-Disposition', $contentDispositionHeader->getFieldName());
    }

    public function testContentDispositionGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('xxx', $contentDispositionHeader->getFieldValue());
    }

    public function testContentDispositionToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Disposition: xxx', $contentDispositionHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

