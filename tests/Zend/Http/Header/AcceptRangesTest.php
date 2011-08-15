<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptRanges;

class AcceptRangesTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptRangesFromStringCreatesValidAcceptRangesHeader()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptRangesHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptRanges', $acceptRangesHeader);
    }

    public function testAcceptRangesGetFieldNameReturnsHeaderName()
    {
        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('Accept-Ranges', $acceptRangesHeader->getFieldName());
    }

    public function testAcceptRangesGetFieldValueReturnsProperValue()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertEquals('bytes', $acceptRangesHeader->getFieldValue());
        $this->assertEquals('bytes', $acceptRangesHeader->getRangeUnit());
    }

    public function testAcceptRangesToStringReturnsHeaderFormattedString()
    {
        $acceptRangesHeader = new AcceptRanges();
        $acceptRangesHeader->setRangeUnit('bytes');

        // @todo set some values, then test output
        $this->assertEquals('Accept-Ranges: bytes', $acceptRangesHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

