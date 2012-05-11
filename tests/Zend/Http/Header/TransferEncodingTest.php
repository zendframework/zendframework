<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\TransferEncoding;

class TransferEncodingTest extends \PHPUnit_Framework_TestCase
{

    public function testTransferEncodingFromStringCreatesValidTransferEncodingHeader()
    {
        $transferEncodingHeader = TransferEncoding::fromString('Transfer-Encoding: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $transferEncodingHeader);
        $this->assertInstanceOf('Zend\Http\Header\TransferEncoding', $transferEncodingHeader);
    }

    public function testTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $transferEncodingHeader = new TransferEncoding();
        $this->assertEquals('Transfer-Encoding', $transferEncodingHeader->getFieldName());
    }

    public function testTransferEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TransferEncoding needs to be completed');

        $transferEncodingHeader = new TransferEncoding();
        $this->assertEquals('xxx', $transferEncodingHeader->getFieldValue());
    }

    public function testTransferEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TransferEncoding needs to be completed');

        $transferEncodingHeader = new TransferEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Transfer-Encoding: xxx', $transferEncodingHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

