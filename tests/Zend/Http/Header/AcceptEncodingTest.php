<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptEncoding;

class AcceptEncodingTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptEncodingFromStringCreatesValidAcceptEncodingHeader()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptEncodingHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptEncoding', $acceptEncodingHeader);
    }

    public function testAcceptEncodingGetFieldNameReturnsHeaderName()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $this->assertEquals('Accept-Encoding', $acceptEncodingHeader->getFieldName());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AcceptEncoding needs to be completed');

        $acceptEncodingHeader = new AcceptEncoding();
        $this->assertEquals('xxx', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AcceptEncoding needs to be completed');

        $acceptEncodingHeader = new AcceptEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Accept-Encoding: xxx', $acceptEncodingHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

