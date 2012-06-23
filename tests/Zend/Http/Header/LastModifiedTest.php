<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\LastModified;

class LastModifiedTest extends \PHPUnit_Framework_TestCase
{

    public function testExpiresFromStringCreatesValidLastModifiedHeader()
    {
        $lastModifiedHeader = LastModified::fromString('Last-Modified: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $lastModifiedHeader);
        $this->assertInstanceOf('Zend\Http\Header\LastModified', $lastModifiedHeader);
    }

    public function testLastModifiedGetFieldNameReturnsHeaderName()
    {
        $lastModifiedHeader = new LastModified();
        $this->assertEquals('Last-Modified', $lastModifiedHeader->getFieldName());
    }

    public function testLastModifiedGetFieldValueReturnsProperValue()
    {
        $lastModifiedHeader = new LastModified();
        $lastModifiedHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $lastModifiedHeader->getFieldValue());
    }

    public function testLastModifiedToStringReturnsHeaderFormattedString()
    {
        $lastModifiedHeader = new LastModified();
        $lastModifiedHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Last-Modified: Sun, 06 Nov 1994 08:49:37 GMT', $lastModifiedHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see ZendTest\Http\Header\DateTest
     */
    
}

