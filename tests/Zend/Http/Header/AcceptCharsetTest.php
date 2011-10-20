<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptCharset;

class AcceptCharsetTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptCharsetFromStringCreatesValidAcceptCharsetHeader()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptCharsetHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptCharset', $acceptCharsetHeader);
    }

    public function testAcceptCharsetGetFieldNameReturnsHeaderName()
    {
        $acceptCharsetHeader = new AcceptCharset();
        $this->assertEquals('Accept-Charset', $acceptCharsetHeader->getFieldName());
    }

    public function testAcceptCharsetGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AcceptCharset needs to be completed');

        $acceptCharsetHeader = new AcceptCharset();
        $this->assertEquals('xxx', $acceptCharsetHeader->getFieldValue());
    }

    public function testAcceptCharsetToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AcceptCharset needs to be completed');

        $acceptCharsetHeader = new AcceptCharset();

        // @todo set some values, then test output
        $this->assertEmpty('Accept-Charset: xxx', $acceptCharsetHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

