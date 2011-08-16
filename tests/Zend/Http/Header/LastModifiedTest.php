<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\LastModified;

class LastModifiedTest extends \PHPUnit_Framework_TestCase
{

    public function testLastModifiedFromStringCreatesValidLastModifiedHeader()
    {
        $lastModifiedHeader = LastModified::fromString('Last-Modified: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $lastModifiedHeader);
        $this->assertInstanceOf('Zend\Http\Header\LastModified', $lastModifiedHeader);
    }

    public function testLastModifiedGetFieldNameReturnsHeaderName()
    {
        $lastModifiedHeader = new LastModified();
        $this->assertEquals('Last-Modified', $lastModifiedHeader->getFieldName());
    }

    public function testLastModifiedGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('LastModified needs to be completed');

        $lastModifiedHeader = new LastModified();
        $this->assertEquals('xxx', $lastModifiedHeader->getFieldValue());
    }

    public function testLastModifiedToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('LastModified needs to be completed');

        $lastModifiedHeader = new LastModified();

        // @todo set some values, then test output
        $this->assertEmpty('Last-Modified: xxx', $lastModifiedHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

